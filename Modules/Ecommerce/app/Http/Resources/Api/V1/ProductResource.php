<?php

namespace Modules\Ecommerce\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \Modules\Ecommerce\Domain\Models\Product $resource
 */
final class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'shop_id' => $this->resource->shop_id,
            'sku' => $this->resource->sku,
            'name' => $this->resource->name,
            'slug' => $this->resource->slug,
            'description' => $this->resource->description,
            'price' => $this->resource->price,
            'compare_at_price' => $this->resource->compare_at_price,
            'cost_price' => $this->resource->cost_price,
            'currency' => $this->resource->currency,
            'stock_qty' => $this->resource->stock_qty,
            'track_inventory' => (bool) $this->resource->track_inventory,
            'allow_backorder' => (bool) $this->resource->allow_backorder,
            'is_active' => (bool) $this->resource->is_active,
            'barcode' => $this->resource->barcode,
            'weight' => $this->resource->weight,
            'length' => $this->resource->length,
            'width' => $this->resource->width,
            'height' => $this->resource->height,
            'meta' => $this->resource->meta,
            'created_at' => $this->resource->created_at?->toISOString(),
            'updated_at' => $this->resource->updated_at?->toISOString(),
            'categories' => $this->whenLoaded('categories', fn () => CategoryResource::collection($this->resource->categories)),
        ];
    }
}
