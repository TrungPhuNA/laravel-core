<?php

namespace Modules\Webhook\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \Modules\Webhook\Domain\Models\WebhookDestination $resource
 */
final class WebhookDestinationResource extends JsonResource
{
    public function toArray($request): array
    {
        $item = $this->resource;

        return [
            'id' => (int) $item->id,
            'webhook_id' => (int) $item->webhook_id,
            'name' => $item->name,
            'url' => $item->url,
            'http_method' => $item->http_method,
            'is_active' => (bool) $item->is_active,
            'type' => $item->type,
            'headers' => $item->headers,
            'send_mode' => $item->send_mode,
            'field_mappings' => $item->field_mappings,
            'drop_mapped_sources' => (bool) $item->drop_mapped_sources,
            'timeout_seconds' => (int) $item->timeout_seconds,
            'created_at' => $item->created_at?->toISOString(),
            'updated_at' => $item->updated_at?->toISOString(),
        ];
    }
}

