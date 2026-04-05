<?php

namespace Modules\Ecommerce\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \Modules\Ecommerce\Domain\Models\Order $resource
 */
final class OrderResource extends JsonResource
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
            'customer_id' => $this->resource->customer_id,
            'status' => $this->resource->status,
            'payment_status' => $this->resource->payment_status,
            'fulfillment_status' => $this->resource->fulfillment_status,
            'subtotal' => $this->resource->subtotal,
            'discount_total' => $this->resource->discount_total,
            'tax_total' => $this->resource->tax_total,
            'shipping_total' => $this->resource->shipping_total,
            'total' => $this->resource->total,
            'currency' => $this->resource->currency,
            'customer_name' => $this->resource->customer_name,
            'customer_email' => $this->resource->customer_email,
            'customer_phone' => $this->resource->customer_phone,
            'customer_snapshot' => $this->resource->customer_snapshot,
            'shipping_address' => $this->resource->shipping_address,
            'billing_address' => $this->resource->billing_address,
            'payment_method' => $this->resource->payment_method,
            'shipping_method' => $this->resource->shipping_method,
            'shipping_provider' => $this->resource->shipping_provider,
            'tracking_number' => $this->resource->tracking_number,
            'note' => $this->resource->note,
            'meta' => $this->resource->meta,
            'placed_at' => $this->resource->placed_at?->toISOString(),
            'paid_at' => $this->resource->paid_at?->toISOString(),
            'cancelled_at' => $this->resource->cancelled_at?->toISOString(),
            'created_at' => $this->resource->created_at?->toISOString(),
            'updated_at' => $this->resource->updated_at?->toISOString(),
            'items' => $this->whenLoaded('items', fn () => OrderItemResource::collection($this->resource->items)),
            'customer' => $this->whenLoaded('customer', fn () => new CustomerResource($this->resource->customer)),
        ];
    }
}
