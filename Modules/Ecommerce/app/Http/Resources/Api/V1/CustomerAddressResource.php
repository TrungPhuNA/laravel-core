<?php

namespace Modules\Ecommerce\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \Modules\Ecommerce\Domain\Models\CustomerAddress $resource
 */
final class CustomerAddressResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'label' => $this->resource->label,
            'name' => $this->resource->name,
            'phone' => $this->resource->phone,
            'line1' => $this->resource->line1,
            'line2' => $this->resource->line2,
            'city' => $this->resource->city,
            'state' => $this->resource->state,
            'postal_code' => $this->resource->postal_code,
            'country' => $this->resource->country,
            'is_default_shipping' => (bool) $this->resource->is_default_shipping,
            'is_default_billing' => (bool) $this->resource->is_default_billing,
            'created_at' => $this->resource->created_at?->toISOString(),
            'updated_at' => $this->resource->updated_at?->toISOString(),
        ];
    }
}

