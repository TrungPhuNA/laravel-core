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
        $descriptionParts = [];

        foreach ($lineItems as $item) {
            $products[] = [
                'name' => Arr::get($item, 'name'),
                'price' => Arr::get($item, 'price'),
            ];

            // Lấy meta data của sản phẩm
            $itemMeta = collect(Arr::get($item, 'meta_data', []));
            $loai = $itemMeta->firstWhere('key', 'pa_loai')['display_value'] ?? '';
            $soPhien = $itemMeta->firstWhere('key', 'pa_so-phien')['display_value'] ?? '';

            // Format thông tin từng sản phẩm theo mẫu
            $itemDesc = [
                Arr::get($item, 'name') . ' × ' . Arr::get($item, 'quantity'),
                "Loại: " . $loai,
                "Số phiên: " . $soPhien,
                "SKU: " . Arr::get($item, 'sku'),
                "Giá: " . number_format((float)Arr::get($item, 'price', 0), 0, '.', '.') . " VND",
                "Tổng cộng: " . number_format((float)Arr::get($item, 'total', 0), 0, '.', '.') . " VND",
            ];
            $descriptionParts[] = implode("\n", array_filter($itemDesc));
        }

        // Ánh xạ trạng thái đơn hàng sang tiếng Việt
        $statusMap = [
            'pending'    => 'Chờ thanh toán',
            'processing' => 'Đang xử lý',
            'on-hold'    => 'Chờ thanh toán',
            'completed'  => 'Đã hoàn thành',
            'cancelled'  => 'Đã hủy',
            'refunded'   => 'Đã hoàn tiền',
            'failed'     => 'Thất bại',
            'checkout-draft' => 'Nháp',
        ];
        $status = Arr::get($payload, 'status');
        $displayStatus = $statusMap[$status] ?? $status;

        // Xây dựng description tổng thể
        $description = implode("\n\n", $descriptionParts);
        $description .= "\n" . implode("\n", [
            "Phương thức thanh toán: " . Arr::get($payload, 'payment_method_title'),
            "Trạng thái: " . $displayStatus,
            "Số hóa đơn: " . Arr::get($payload, 'billing_bill_number'),
            "Số hợp đồng: " . Arr::get($payload, 'billing_contract_number'),
            "Link hợp đồng: " . Arr::get($payload, 'billing_contract_link'),
        ]);

        return [
            'request_name' => 'Đơn hàng WooCommerce #' . Arr::get($payload, 'id').  ' - ' .Arr::get($billing, 'company'),
            'order_id' => (string) Arr::get($payload, 'id'),
            'contact_name' => Arr::get($billing,'last_name'),
            'contact_email' => Arr::get($billing, 'email'),
            'contact_phone' => Arr::get($billing, 'phone'),
            'company_name' => Arr::get($billing, 'company'),
            'total_amount' => (float) Arr::get($payload, 'total', 0),
            'campaign_source' => $getMeta('_wc_order_attribution_utm_source'),
            'campaign_medium' => $getMeta('_wc_order_attribution_utm_medium'),
            'campaign_name' => $getMeta('_wc_order_attribution_utm_campaign'),
            'campaign_content' => $getMeta('_wc_order_attribution_utm_content'),
            'products' => $products,
            'description' => trim($description),
            
            // Một số trường metadata bổ sung
            'payment_account' => Arr::get($payload, 'payment_method_title'),
            'note' => Arr::get($payload, 'customer_note'),
        ];
    }
}
