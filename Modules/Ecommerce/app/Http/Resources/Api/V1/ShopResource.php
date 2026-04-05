<?php

namespace Modules\Ecommerce\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \Modules\Ecommerce\Domain\Models\Shop $resource
 */
final class ShopResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'code' => $this->resource->code,
            'name' => $this->resource->name,
            'domain' => $this->resource->domain,
            'timezone' => $this->resource->timezone,
            'currency' => $this->resource->currency,
            'is_active' => (bool) $this->resource->is_active,
            'meta' => $this->resource->meta,
            'created_at' => $this->resource->created_at?->toISOString(),
            'updated_at' => $this->resource->updated_at?->toISOString(),
        ];
    }
}

