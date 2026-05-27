<?php

namespace Modules\Webhook\Application\Services;

use App\Core\Exceptions\ApiException;
use App\Core\Exceptions\ErrorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
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
        Log::info('=== WEBHOOK RAW RECEIVE START ===', $this->cleanData([
            'public_id' => $publicId,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'params' => $request->all(),
            'raw_body' => $request->getContent(),
        ]));

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

            // Kiểm tra hành vi spam đặt đơn hàng (áp dụng đặc biệt cho WooCommerce webhook)
            $this->checkSpam($webhook, $payload);

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
        $signature = $request->header('x-wc-webhook-signature') ?? $request->header('X-WC-Webhook-Signature');

        Log::info('WooCommerce Webhook Request Debug', [
            'headers' => $request->headers->all(),
            'signature' => $signature,
            'content_type' => $request->header('Content-Type'),
            'body_raw' => substr($request->getContent(), 0, 500),
            'body_all' => $request->all(),
        ]);

        // 1. Nếu có signature header -> Thực hiện kiểm tra chữ ký (HMAC hoặc Token Hash)
        if ($signature) {
            // Kiểm tra HMAC (nếu có secret)
            if ($webhook->auth_secret_encrypted) {
                try {
                    $secret = Crypt::decryptString($webhook->auth_secret_encrypted);
                    $payload = $request->getContent(); // Lấy raw body cho cả JSON và Form-urlencoded
                    $expectedSignature = base64_encode(hash_hmac('sha256', $payload, $secret, true));

                    if (hash_equals($expectedSignature, $signature)) {
                        return;
                    }
                } catch (\Throwable) { }
            }

            // Kiểm tra Token Hash (so khớp trực tiếp chữ ký với token đã lưu)
            if ($webhook->auth_token_hash && Hash::check($signature, $webhook->auth_token_hash)) {
                return;
            }
        }

        // 2. Nếu không có signature header (như lúc Ping test), thử fallback check token từ Query hoặc Body
        $fallbackToken = (string) ($request->query('token') ?? $request->input('token') ?? '');
        if ($fallbackToken !== '' && $webhook->auth_token_hash) {
            if (Hash::check($fallbackToken, $webhook->auth_token_hash)) {
                return;
            }
        }

        throw new ApiException(
            errorCode: ErrorCode::UNAUTHORIZED->value,
            message: 'Xác thực WooCommerce thất bại (Chữ ký/Token không hợp lệ hoặc thiếu)',
            status: 401,
        );
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
            'body' => $this->cleanData($request->getContent()),
            'mapped_payload' => $this->cleanData($mappedPayload),
            'status' => $status,
            'error_type' => $errorType,
            'error_message' => $this->cleanData($errorMessage),
            'received_at' => now(),
        ]);

        return $item;
    }

    /**
     * Clean dữ liệu để đảm bảo UTF-8 hợp lệ
     */
    private function cleanData(mixed $data): mixed
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->cleanData($value);
            }
        } elseif (is_string($data)) {
            return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        }

        return $data;
    }

    /**
     * Mục đích: Kiểm tra và chặn các request spam đặt đơn hàng gửi qua Webhook (đặc biệt là WooCommerce).
     * 
     * Logic xử lý chính:
     * - Chỉ kiểm tra nếu tính năng được bật trong cấu hình (`config.spam.enabled`) và webhook có loại là 'woocommerce_at'.
     * - Trích xuất Số điện thoại người mua hàng (`billing.phone`) và danh sách SKU sản phẩm (`line_items.*.sku`).
     * - Với mỗi SKU, tạo một Cache Key nguyên tử (atomic) kết hợp: `webhook:spam:phone_sku:{public_id}:{phone}:{sku}`.
     * - Nếu không có số điện thoại, fallback về sử dụng IP của client gửi request: `webhook:spam:sku_ip:{public_id}:{ip}:{sku}`.
     * - Sử dụng `Cache::add()` với thời gian khóa (lock time) được lấy từ cấu hình (mặc định 3 giây).
     * - Nếu `Cache::add()` trả về false, nghĩa là đã có một thao tác tương tự diễn ra trong vòng 3 giây trước đó -> Xác định là SPAM -> Ném ra ngoại lệ `ApiException` (mã lỗi `SPAM_BLOCKED`, HTTP 429).
     * 
     * Các case đặc biệt:
     * - Không có line_items hoặc không có SKU: Bỏ qua kiểm tra để tránh chặn nhầm các request ping hoặc test rỗng.
     * - Một đơn hàng có nhiều sản phẩm (nhiều SKU): Kiểm tra độc lập từng SKU trong danh sách unique.
     * - Ký tự đặc biệt trong SĐT hoặc SKU: Sử dụng regex lọc bỏ ký tự đặc biệt để đảm bảo khóa cache hoạt động an toàn và ổn định trên mọi Driver (như Redis, File, v.v.).
     */
    private function checkSpam(Webhook $webhook, array $payload): void
    {
        // Case đặc biệt 1: Tính năng check spam bị tắt trong cấu hình
        if (!config('webhook.spam.enabled', true)) {
            return;
        }

        // Chỉ áp dụng check spam cho loại webhook WooCommerce 'woocommerce_at'
        if ($webhook->type !== 'woocommerce_at') {
            return;
        }

        $phone = trim((string) Arr::get($payload, 'billing.phone', ''));
        $lineItems = Arr::get($payload, 'line_items', []);

        // Case đặc biệt 2: Nếu payload không chứa danh sách sản phẩm, bỏ qua check spam
        if (empty($lineItems) || !is_array($lineItems)) {
            return;
        }

        // Lấy thời gian khóa từ cấu hình (mặc định 3 giây)
        $lockTime = (int) config('webhook.spam.lock_time', 3);
        if ($lockTime <= 0) {
            return;
        }

        // Trích xuất tất cả SKU hợp lệ trong đơn hàng
        $skus = [];
        foreach ($lineItems as $item) {
            $sku = trim((string) Arr::get($item, 'sku', ''));
            if ($sku !== '') {
                $skus[] = $sku;
            }
        }

        // Case đặc biệt 3: Không tìm thấy bất kỳ SKU nào trong đơn hàng, bỏ qua check spam
        if (empty($skus)) {
            return;
        }

        // Lọc bỏ SKU trùng lặp trong cùng một request đơn hàng
        $uniqueSkus = array_unique($skus);

        // Duyệt qua từng SKU để kiểm tra trùng lặp đặt đơn nhanh liên tiếp
        foreach ($uniqueSkus as $sku) {
            // Chuẩn hóa SĐT và SKU: loại bỏ các ký tự đặc biệt để tránh lỗi tên cache key trên các driver như Redis
            $safePhone = preg_replace('/[^0-9]/', '', $phone);
            $safeSku = preg_replace('/[^A-Za-z0-9_\-]/', '', $sku);
            
            if ($safePhone === '') {
                // Case đặc biệt 4: Nếu không có SĐT khách hàng, fallback về check theo IP người dùng gửi webhook để chống spam IP
                $ip = request()->ip() ?? '127.0.0.1';
                $safeIp = preg_replace('/[^0-9\.]/', '', $ip);
                $cacheKey = "webhook:spam:sku_ip:{$webhook->public_id}:{$safeIp}:{$safeSku}";
            } else {
                $cacheKey = "webhook:spam:phone_sku:{$webhook->public_id}:{$safePhone}:{$safeSku}";
            }

            // Cache::add() hoạt động atomic (nguyên tử) giúp ngăn chặn race condition tuyệt đối.
            // Trả về true nếu ghi key thành công (lần đầu đặt đơn).
            // Trả về false nếu key đã tồn tại (đã đặt trùng trong thời gian khóa lockTime).
            $isSuccess = Cache::add($cacheKey, true, $lockTime);

            if (!$isSuccess) {
                // Ghi log cảnh báo hành vi spam
                Log::warning('=== WEBHOOK SPAM DETECTED & BLOCKED ===', [
                    'webhook_id' => $webhook->id,
                    'public_id' => $webhook->public_id,
                    'phone' => $phone,
                    'sku' => $sku,
                    'cache_key' => $cacheKey,
                    'lock_time' => $lockTime,
                    'ip' => request()->ip(),
                ]);

                // Ném ngoại lệ chặn request, HTTP 429 Too Many Requests
                throw new ApiException(
                    errorCode: ErrorCode::SPAM_BLOCKED->value,
                    message: "Phát hiện hành vi spam đặt đơn liên tục cho SKU [{$sku}]. Vui lòng thử lại sau {$lockTime} giây.",
                    status: 429
                );
            }
        }
    }
}
