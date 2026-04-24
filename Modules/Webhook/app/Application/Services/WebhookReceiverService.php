<?php

namespace Modules\Webhook\Application\Services;

use App\Core\Exceptions\ApiException;
use App\Core\Exceptions\ErrorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\Webhook\Application\Contracts\WebhookReceiverServiceInterface;
use Modules\Webhook\Domain\Models\Webhook;
use Modules\Webhook\Domain\Models\WebhookRequest;
use Modules\Webhook\Infrastructure\Contracts\WebhookRepositoryInterface;
use Modules\Webhook\Jobs\DispatchWebhookDestinationsJob;

final class WebhookReceiverService implements WebhookReceiverServiceInterface
{
    public function __construct(
        private readonly WebhookRepositoryInterface $webhooks,
    ) {}

    public function receive(string $publicId, Request $request): array
    {
        $webhook = $this->webhooks->findByPublicIdOrFail($publicId);

        try {
            if (!$webhook->is_active) {
                throw new ApiException(
                    errorCode: ErrorCode::FORBIDDEN->value,
                    message: 'Webhook đang bị tắt',
                    status: 403,
                );
            }

            $method = strtoupper((string)$request->method());
            $allowed = $this->normalizeAllowedMethods($webhook);

            if (!in_array($method, $allowed, true)) {
                throw new ApiException(
                    errorCode: ErrorCode::METHOD_NOT_ALLOWED->value,
                    message: 'Webhook không hỗ trợ method này',
                    status: 405,
                    details: ['method' => $method, 'allowed' => $allowed],
                );
            }

            $this->checkAuth($webhook, $request);

            // Payload validate: hop nhat query + body (Laravel: $request->all()).
            // Neu auth dung query param token=... -> remove de khong can validate.
            $payload = Arr::except($request->all(), ['token']);

            $validated = $payload;

            if ($webhook->type === 'default') {
                $rules = $webhook->validation_rules;
                if (is_array($rules) && $rules !== []) {
                    $validated = Validator::make($payload, $rules)->validate();
                }
            } else {
                // Xử lý payload theo từng loại kênh (case-by-case)
                $validated = match ($webhook->type) {
                    'woocommerce_at' => \Modules\Webhook\Application\Mappers\WooCommerceAtMapper::map($payload),
                    default => $payload, // Mặc định trả về raw payload nếu chưa implement mapper
                };
            }

            // Success log
            $requestLog = $this->logRequest($webhook, $request, 'success', null, null, $validated);
            $webhook->forceFill(['last_received_at' => now()])->save();

            // Fan-out (async by queue driver). This is best-effort and must not break receiver.
            try {
                DispatchWebhookDestinationsJob::dispatch((int) $webhook->id, (int) $requestLog->id, $validated);
            } catch (\Throwable) {
                // ignore
            }

            return ['webhook' => $webhook, 'validated' => $validated];

        } catch (ApiException $e) {
            $this->logRequest($webhook, $request, 'failed', $e->getErrorCode(), $e->getMessage());
            throw $e;
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->logRequest($webhook, $request, 'failed', 'validation_failed', $e->getMessage());
            throw $e;
        } catch (\Throwable $e) {
            $this->logRequest($webhook, $request, 'failed', 'system_error', $e->getMessage());
            throw $e;
        }
    }

    /**
     * @return list<string>
     */
    private function normalizeAllowedMethods(Webhook $webhook): array
    {
        $methods = $webhook->allowed_methods;
        if (!is_array($methods) || $methods === []) {
            return ['GET', 'POST'];
        }

        $out = [];
        foreach ($methods as $m) {
            $m = strtoupper((string) $m);
            if (in_array($m, ['GET', 'POST'], true)) {
                $out[] = $m;
            }
        }

        return $out !== [] ? array_values(array_unique($out)) : ['GET', 'POST'];
    }

    private function checkAuth(Webhook $webhook, Request $request): void
    {
        if ($webhook->auth_type === 'none') {
            return;
        }

        // Ưu tiên xử lý check auth thủ công cho các loại webhook đặc thù
        if ($webhook->type !== 'default') {
            match ($webhook->type) {
                'woocommerce_at' => $this->checkWooCommerceAtAuth($webhook, $request),
                default => null,
            };
            return;
        }

        if ($webhook->auth_type === 'token') {
            $token = (string) ($request->header('X-Webhook-Token')
                ?? $request->query('token')
                ?? '');
            $token = trim($token);

            if ($token === '' || !$webhook->auth_token_hash || !Hash::check($token, $webhook->auth_token_hash)) {
                throw new ApiException(
                    errorCode: ErrorCode::UNAUTHORIZED->value,
                    message: 'Webhook token không hợp lệ',
                    status: 401,
                );
            }

            return;
        }

        if ($webhook->auth_type === 'hmac') {
            if (!$webhook->auth_secret_encrypted) {
                throw new ApiException(
                    errorCode: ErrorCode::UNAUTHORIZED->value,
                    message: 'Webhook chưa có secret để verify signature',
                    status: 401,
                );
            }

            $tsHeader = config('webhook.hmac.headers.timestamp', 'X-Webhook-Timestamp');
            $sigHeader = config('webhook.hmac.headers.signature', 'X-Webhook-Signature');
            $maxSkew = (int) config('webhook.hmac.max_skew_seconds', 300);

            $timestamp = (string) ($request->header($tsHeader) ?? '');
            $signature = (string) ($request->header($sigHeader) ?? '');

            $timestamp = trim($timestamp);
            $signature = trim($signature);

            if ($timestamp === '' || $signature === '') {
                throw new ApiException(
                    errorCode: ErrorCode::UNAUTHORIZED->value,
                    message: 'Thiếu header chữ ký webhook',
                    status: 401,
                );
            }

            if (!ctype_digit($timestamp)) {
                throw new ApiException(
                    errorCode: ErrorCode::UNAUTHORIZED->value,
                    message: 'Timestamp không hợp lệ',
                    status: 401,
                );
            }

            $ts = (int) $timestamp;
            $now = Carbon::now()->timestamp;
            if (abs($now - $ts) > $maxSkew) {
                throw new ApiException(
                    errorCode: ErrorCode::UNAUTHORIZED->value,
                    message: 'Timestamp vượt quá giới hạn cho phép',
                    status: 401,
                );
            }

            $secret = Crypt::decryptString($webhook->auth_secret_encrypted);

            $expected = $this->computeSignature($request, $ts, $secret);
            $given = $this->normalizeSignature($signature);

            if ($given === '' || !hash_equals($expected, $given)) {
                throw new ApiException(
                    errorCode: ErrorCode::UNAUTHORIZED->value,
                    message: 'Chữ ký webhook không hợp lệ',
                    status: 401,
                );
            }

            return;
        }
    }

    private function computeSignature(Request $request, int $timestamp, string $secret): string
    {
        $method = strtoupper((string) $request->method());
        $path = (string) $request->getPathInfo(); // includes publicId
        $query = (string) ($request->getQueryString() ?? '');
        $body = (string) $request->getContent();

        // Canonical string: timestamp\nMETHOD\nPATH\nQUERY\nBODY
        $canonical = $timestamp."\n".$method."\n".$path."\n".$query."\n".$body;

        return hash_hmac('sha256', $canonical, $secret);
    }

    private function normalizeSignature(string $value): string
    {
        // Accept formats:
        // - "sha256=<hex>"
        // - "<hex>"
        $value = trim($value);
        if (str_starts_with($value, 'sha256=')) {
            $value = substr($value, strlen('sha256='));
        }
        return trim($value);
    }

    private function checkWooCommerceAtAuth(Webhook $webhook, Request $request): void
    {
        $signature = $request->header('x-wc-webhook-signature');

        // WooCommerce thường dùng cơ chế HMAC với Secret
        if (!$webhook->auth_secret_encrypted) {
            throw new ApiException(
                errorCode: ErrorCode::UNAUTHORIZED->value,
                message: 'Webhook WooCommerce chưa cấu hình Secret',
                status: 401,
            );
        }

        if (!$signature) {
            throw new ApiException(
                errorCode: ErrorCode::UNAUTHORIZED->value,
                message: 'Thiếu x-wc-webhook-signature trong header',
                status: 401,
            );
        }

        $secret = Crypt::decryptString($webhook->auth_secret_encrypted);
        $payload = $request->getContent();

        // Verify WooCommerce signature: base64(hmac-sha256(payload, secret))
        $expectedSignature = base64_encode(hash_hmac('sha256', $payload, $secret, true));

        if (!hash_equals($expectedSignature, $signature)) {
            throw new ApiException(
                errorCode: ErrorCode::UNAUTHORIZED->value,
                message: 'WooCommerce signature không hợp lệ',
                status: 401,
            );
        }
    }

    private function logRequest(
        Webhook $webhook,
        Request $request,
        string $status = 'success',
        ?string $errorType = null,
        ?string $errorMessage = null,
        ?array $mappedPayload = null
    ): WebhookRequest {
        $headers = $request->headers->all();

        // Mask thong tin nhay cam.
        unset($headers['authorization'], $headers['x-webhook-token'], $headers['cookie']);

        /** @var WebhookRequest $item */
        $item = WebhookRequest::query()->create([
            'webhook_id' => $webhook->id,
            'method' => strtoupper((string)$request->method()),
            'ip' => (string)($request->ip() ?? ''),
            'headers' => $headers,
            'query' => $request->query(),
            'body' => $request->getContent(),
            'mapped_payload' => $mappedPayload,
            'status' => $status,
            'error_type' => $errorType,
            'error_message' => $errorMessage,
            'received_at' => now(),
        ]);

        return $item;
    }
}
