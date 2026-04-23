<?php

namespace Modules\Webhook\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \Modules\Webhook\Domain\Models\WebhookRequest $resource
 */
final class WebhookRequestResource extends JsonResource
{
    public function toArray($request): array
    {
        $item = $this->resource;

        return [
            'id' => $item->id,
            'webhook_id' => (int) $item->webhook_id,
            'method' => $item->method,
            'ip' => $item->ip,
            'status' => $item->status,
            'error_type' => $item->error_type,
            'error_message' => $item->error_message,
            'received_at' => $item->received_at?->toISOString(),
            'body_preview' => $this->truncate((string) $item->body, 500),
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

