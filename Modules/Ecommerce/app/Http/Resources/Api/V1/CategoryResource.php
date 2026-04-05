<?php

namespace Modules\Ecommerce\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \Modules\Ecommerce\Domain\Models\Category $resource
 */
final class CategoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'shop_id' => $this->resource->shop_id,
            'parent_id' => $this->resource->parent_id,
            'name' => $this->resource->name,
            'slug' => $this->resource->slug,
            'description' => $this->resource->description,
            'image_url' => $this->resource->image_url,
            'seo_title' => $this->resource->seo_title,
            'seo_description' => $this->resource->seo_description,
            'position' => $this->resource->position,
            'is_active' => (bool) $this->resource->is_active,
            'created_at' => $this->resource->created_at?->toISOString(),
            'updated_at' => $this->resource->updated_at?->toISOString(),
            'parent' => $this->whenLoaded('parent', fn () => new self($this->resource->parent)),
            'children' => $this->whenLoaded('children', fn () => self::collection($this->resource->children)),
        ];
    }
}
