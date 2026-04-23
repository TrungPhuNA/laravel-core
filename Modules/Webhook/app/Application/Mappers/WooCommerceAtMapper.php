<?php

namespace Modules\Webhook\Application\Mappers;

use Illuminate\Support\Arr;

final class WooCommerceAtMapper
{
    /**
     * Map payload của WooCommerce thành format chuẩn của hệ thống
     */
    public static function map(array $payload): array
    {
        $billing = Arr::get($payload, 'billing', []);
        $contactName = trim(Arr::get($billing, 'last_name', '') . ' ' . Arr::get($billing, 'first_name', ''));
        
        $metaData = collect(Arr::get($payload, 'meta_data', []));
        $getMeta = fn($key) => $metaData->firstWhere('key', $key)['value'] ?? null;

        $products = [];
        $lineItems = Arr::get($payload, 'line_items', []);
        foreach ($lineItems as $item) {
            $products[] = [
                'name' => Arr::get($item, 'name'),
                'price' => Arr::get($item, 'price'),
            ];
        }

        return [
            'request_name' => 'Đơn hàng WooCommerce #' . Arr::get($payload, 'id'),
            'order_id' => (string) Arr::get($payload, 'id'),
            'contact_name' => $contactName,
            'contact_email' => Arr::get($billing, 'email'),
            'contact_phone' => Arr::get($billing, 'phone'),
            'total_amount' => (float) Arr::get($payload, 'total', 0),
            'campaign_source' => $getMeta('_wc_order_attribution_utm_source'),
            'campaign_medium' => $getMeta('_wc_order_attribution_utm_medium'),
            'campaign_name' => $getMeta('_wc_order_attribution_utm_campaign'),
            'campaign_content' => $getMeta('_wc_order_attribution_utm_content'),
            'products' => $products,
            
            // Một số trường metadata bổ sung
            'payment_account' => Arr::get($payload, 'payment_method_title'),
            'note' => Arr::get($payload, 'customer_note'),
        ];
    }
}
