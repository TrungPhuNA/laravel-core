<?php

namespace Modules\Ecommerce\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \Modules\Ecommerce\Domain\Models\Customer $resource
 */
final class CustomerResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'shop_id' => $this->resource->shop_id,
            'code' => $this->resource->code,
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'phone' => $this->resource->phone,
            'gender' => $this->resource->gender,
            'birthday' => $this->resource->birthday?->toDateString(),
            'tags' => $this->resource->tags,
            'note' => $this->resource->note,
            'created_at' => $this->resource->created_at?->toISOString(),
            'updated_at' => $this->resource->updated_at?->toISOString(),
            'addresses' => $this->whenLoaded('addresses', fn () => CustomerAddressResource::collection($this->resource->addresses)),
        ];
    }
}
