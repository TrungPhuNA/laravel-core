<?php

namespace Modules\Ecommerce\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \Modules\Ecommerce\Domain\Models\OrderItem $resource
 */
final class OrderItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'shop_id' => $this->resource->shop_id,
            'product_id' => $this->resource->product_id,
            'variant_id' => $this->resource->variant_id,
            'sku' => $this->resource->sku,
            'name' => $this->resource->name,
            'quantity' => $this->resource->quantity,
            'unit_price' => $this->resource->unit_price,
            'total_price' => $this->resource->total_price,
            'discount_total' => $this->resource->discount_total,
            'tax_total' => $this->resource->tax_total,
            'meta' => $this->resource->meta,
            'created_at' => $this->resource->created_at?->toISOString(),
            'updated_at' => $this->resource->updated_at?->toISOString(),
        ];
    }
}
