<?php

namespace Modules\Webhook\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \Modules\Webhook\Domain\Models\WebhookDispatchLog $resource
 */
final class WebhookDispatchLogResource extends JsonResource
{
    public function toArray($request): array
    {
        $item = $this->resource;

        return [
            'id' => (int) $item->id,
            'webhook_id' => (int) $item->webhook_id,
            'webhook_request_id' => (int) $item->webhook_request_id,
            'destination_id' => (int) $item->destination_id,
            'status' => $item->status,
            'dispatched_at' => $item->dispatched_at?->toISOString(),
            'duration_ms' => $item->duration_ms,
            'request_body_preview' => $this->truncate((string) ($item->request_body ?? ''), 500),
            'response_status' => $item->response_status,
            'response_body_preview' => $this->truncate((string) ($item->response_body ?? ''), 500),
            'error_type' => $item->error_type,
            'error_message' => $item->error_message,
            'created_at' => $item->created_at?->toISOString(),
        ];
    }

    private function truncate(string $value, int $max): string
    {
        $value = trim($value);
        if (strlen($value) <= $max) {
            return $value;
        }

        return substr($value, 0, $max).'...';
    }
}

