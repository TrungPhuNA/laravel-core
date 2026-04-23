<?php

namespace Modules\Webhook\Application\Services;

use Illuminate\Support\Facades\Http;
use Modules\Webhook\Application\Contracts\WebhookForwarderServiceInterface;
use Modules\Webhook\Domain\Models\WebhookDispatchLog;
use Modules\Webhook\Domain\Models\WebhookDestination;
use Modules\Webhook\Infrastructure\Contracts\WebhookDestinationRepositoryInterface;

final class WebhookForwarderService implements WebhookForwarderServiceInterface
{
    public function __construct(
        private readonly WebhookDestinationRepositoryInterface $destinations,
    ) {}

    public function dispatch(int $webhookId, int $webhookRequestId, array $payload): int
    {
        $items = $this->destinations->listActiveForWebhook($webhookId);
        if ($items->isEmpty()) {
            return 0;
        }

        $maxReqBytes = (int) config('webhook.forwarder.max_request_log_bytes', 50_000);
        $maxResBytes = (int) config('webhook.forwarder.max_response_log_bytes', 50_000);

        $count = 0;
        /** @var WebhookDestination $dest */
        foreach ($items as $dest) {
            $count++;

            $out = $this->buildPayload($payload, $dest);
            $requestBody = $this->truncateBytes(json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '', $maxReqBytes);

            /** @var WebhookDispatchLog $log */
            $log = WebhookDispatchLog::query()->create([
                'webhook_id' => $webhookId,
                'webhook_request_id' => $webhookRequestId,
                'destination_id' => (int) $dest->id,
                'status' => 'pending',
                'dispatched_at' => now(),
                'request_body' => $requestBody,
            ]);

            $start = hrtime(true);
            try {
                $method = strtoupper((string) $dest->http_method ?: 'POST');

                $client = Http::timeout((int) ($dest->timeout_seconds ?: 10))
                    ->acceptJson()
                    ->withHeaders($this->normalizeHeaders($dest->headers));

                $res = $client->send($method, (string) $dest->url, ['json' => $out]);

                $durationMs = (int) round((hrtime(true) - $start) / 1_000_000);
                $log->forceFill([
                    'duration_ms' => $durationMs,
                    'response_status' => $res->status(),
                    'response_headers' => $res->headers(),
                    'response_body' => $this->truncateBytes((string) $res->body(), $maxResBytes),
                    'status' => $res->successful() ? 'success' : 'failed',
                ])->save();
            } catch (\Throwable $e) {
                $durationMs = (int) round((hrtime(true) - $start) / 1_000_000);
                $log->forceFill([
                    'duration_ms' => $durationMs,
                    'status' => 'failed',
                    'error_type' => 'exception',
                    'error_message' => $e->getMessage(),
                ])->save();
            }
        }

        return $count;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function buildPayload(array $payload, WebhookDestination $dest): array
    {
        return match ($dest->type) {
            'use_mapped' => $payload,
            'woocommerce_at_forward' => $this->mapWooCommerceAtForward($payload),
            default => $this->applyGenericMappings($payload, $dest),
        };
    }

    /**
     * Ví dụ: Xử lý body đặc thù cho type woocommerce_at_forward
     */
    private function mapWooCommerceAtForward(array $payload): array
    {
        // Giả sử partner yêu cầu wrap trong key 'data' và thêm 'timestamp'
        return [
            'data' => $payload,
            'forwarded_at' => now()->toDateTimeString(),
            'source' => 'webhook_core',
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function applyGenericMappings(array $payload, WebhookDestination $dest): array
    {
        $mappings = is_array($dest->field_mappings) ? $dest->field_mappings : [];
        $mapped = [];

        foreach ($mappings as $item) {
            if (!is_array($item)) {
                continue;
            }
            $from = isset($item['from']) ? trim((string) $item['from']) : '';
            $to = isset($item['to']) ? trim((string) $item['to']) : '';
            if ($from === '' || $to === '') {
                continue;
            }
            if (!array_key_exists($from, $payload)) {
                continue;
            }
            $mapped[$to] = $payload[$from];
        }

        $sendMode = (string) ($dest->send_mode ?: 'merge');
        if ($sendMode === 'mapped_only') {
            return $mapped;
        }

        $out = $payload;
        foreach ($mapped as $k => $v) {
            $out[$k] = $v;
        }

        if ($dest->drop_mapped_sources && $mappings !== []) {
            foreach ($mappings as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $from = isset($item['from']) ? trim((string) $item['from']) : '';
                if ($from !== '' && array_key_exists($from, $out)) {
                    unset($out[$from]);
                }
            }
        }

        return $out;
    }

    /**
     * @param array<string, mixed>|null $headers
     * @return array<string, string>
     */
    private function normalizeHeaders(?array $headers): array
    {
        $out = [];
        if (!$headers) {
            return $out;
        }
        foreach ($headers as $k => $v) {
            $key = trim((string) $k);
            if ($key === '') {
                continue;
            }
            $out[$key] = is_scalar($v) ? (string) $v : json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return $out;
    }

    private function truncateBytes(string $value, int $maxBytes): string
    {
        if ($maxBytes <= 0) {
            return '';
        }
        if (strlen($value) <= $maxBytes) {
            return $value;
        }
        return substr($value, 0, $maxBytes);
    }
}

