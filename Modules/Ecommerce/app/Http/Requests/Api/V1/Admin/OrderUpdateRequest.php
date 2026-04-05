<?php

namespace Modules\Ecommerce\Http\Requests\Api\V1\Admin;

use App\Core\Http\Requests\ApiFormRequest;

final class OrderUpdateRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'customer_id' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'status' => ['sometimes', 'nullable', 'string', 'max:30'],
            'payment_status' => ['sometimes', 'nullable', 'string', 'max:30'],
            'fulfillment_status' => ['sometimes', 'nullable', 'string', 'max:30'],
            'currency' => ['sometimes', 'nullable', 'string', 'size:3'],
            'customer_email' => ['sometimes', 'nullable', 'email', 'max:200'],
            'customer_phone' => ['sometimes', 'nullable', 'string', 'max:40'],
            'shipping_address' => ['sometimes', 'nullable', 'array'],
            'billing_address' => ['sometimes', 'nullable', 'array'],
            'note' => ['sometimes', 'nullable', 'string', 'max:255'],
            'placed_at' => ['sometimes', 'nullable', 'date'],
            'paid_at' => ['sometimes', 'nullable', 'date'],
            'cancelled_at' => ['sometimes', 'nullable', 'date'],
            'items' => ['sometimes', 'array', 'min:1'],
            'items.*.product_id' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'items.*.variant_id' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'items.*.sku' => ['sometimes', 'nullable', 'string', 'max:80'],
            'items.*.name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.unit_price' => ['required_with:items', 'numeric', 'min:0'],
        ];
    }
}
