<?php

namespace Modules\Webhook\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \Modules\Webhook\Domain\Models\Webhook $resource
 */
final class WebhookResource extends JsonResource
{
    public function toArray($request): array
    {
        $wh = $this->resource;

        return [
            'id' => $wh->id,
            'user_id' => (int) $wh->user_id,
            'name' => $wh->name,
            'type' => $wh->type,
            'public_id' => $wh->public_id,
            'is_active' => (bool) $wh->is_active,
            'allowed_methods' => $wh->allowed_methods,
            'auth_type' => $wh->auth_type,
            'has_auth_secret' => $wh->auth_secret_encrypted !== null,
            'validation_rules' => $wh->validation_rules,
            'description' => $wh->description,
            'last_received_at' => $wh->last_received_at?->toISOString(),
            'created_at' => $wh->created_at?->toISOString(),
            'updated_at' => $wh->updated_at?->toISOString(),
        ];
    }
}
