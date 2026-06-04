<?php

namespace Modules\Webhook\Application\Mappers;

use Illuminate\Support\Arr;

final class WooCommerceAtMapper
{
    /**
     * Mục đích: Danh sách sản phẩm dịch vụ của hệ thống dùng để tra cứu dựa trên SKU sạch.
     */
    private static array $SERVICES_LIST = [
        [
            'value' => 'GT_AMBASSADOR',
            'name' => 'GT - Ambassador Community',
            'service_type' => 'GT',
            'unit' => 'VIEW',
            'old_ids' => [1]
        ],
        [
            'value' => 'GT_PT_BANNER_GAMIFICATION',
            'name' => 'GT - Premium Traffic (Banner & Gamification)',
            'service_type' => 'GT',
            'unit' => 'CLICK',
            'old_ids' => [2, 3]
        ],
        [
            'value' => 'GT_BOOKING_KOC',
            'name' => 'GT - Booking KOC',
            'service_type' => 'GT',
            'unit' => 'PACKAGE',
            'old_ids' => []
        ],
        [
            'value' => 'GT_BRAND_SERVICE',
            'name' => 'GT - Other Branding Services',
            'service_type' => 'GT',
            'unit' => 'PACKAGE',
            'old_ids' => []
        ],
        [
            'value' => 'GA_QLG',
            'name' => 'GA - Qualified Lead Generation',
            'service_type' => 'GA',
            'unit' => 'LEAD',
            'old_ids' => [4]
        ],
        [
            'value' => 'GA_QUG',
            'name' => 'GA - Qualified User Generation',
            'service_type' => 'GA',
            'unit' => 'NEW_USER',
            'old_ids' => [5, 6]
        ],
        [
            'value' => 'GA_FIRST_TRANSACTION',
            'name' => 'GA - First Transaction',
            'service_type' => 'GA',
            'unit' => 'FIRST_TRANSACTION',
            'old_ids' => []
        ],
        [
            'value' => 'GA_CPV',
            'name' => 'GA - Cost per Voucher',
            'service_type' => 'GA',
            'old_ids' => []
        ],
        [
            'value' => 'GS_LIVE_HUB',
            'name' => 'GS - Live Hub',
            'service_type' => 'GS',
            'old_ids' => [9]
        ],
        [
            'value' => 'GS_CPS',
            'name' => 'GS - Cost per sale',
            'service_type' => 'GS',
            'old_ids' => [7, 8, 10]
        ],
        [
            'value' => 'GLC_PT_CVG',
            'name' => 'GLC - Premium Partnership Platform (Cashback & Voucher & Gamification)',
            'unit' => 'ORDER',
            'service_type' => 'GLC',
            'old_ids' => [11, 12, 13, 14]
        ],
        [
            'value' => 'GU_SCALEF',
            'name' => 'GU - ScaleF (Platform as a Service)',
            'service_type' => 'GU',
            'old_ids' => [15, 16]
        ],
        [
            'value' => 'GU_AT_ACADEMY',
            'name' => 'GU - Academy',
            'service_type' => 'GU',
            'old_ids' => [17]
        ]
    ];

    /**
     * Map payload của WooCommerce thành format chuẩn của hệ thống
     */
    public static function map(array $payload): array
    {
        $billing = Arr::get($payload, 'billing', []);

        // Mục đích: Xác định tên người liên hệ mua hàng.
        // Logic xử lý chính: Ưu tiên lấy last_name của khách hàng, nếu không có thì fallback lấy first_name.
        $lastName = trim((string) Arr::get($billing, 'last_name', ''));
        $firstName = trim((string) Arr::get($billing, 'first_name', ''));
        $contactName = $lastName !== '' ? $lastName : $firstName;

        // Mục đích: Xây dựng địa chỉ đầy đủ của khách hàng từ thông tin thanh toán (billing).
        // Logic xử lý chính: 
        // - Trích xuất các trường address_1, city, state từ mảng $billing và cắt khoảng trắng thừa.
        // - Lọc bỏ các trường rỗng để tránh dư thừa dấu phẩy.
        // - Ghép nối các phần tử bằng dấu phẩy và khoảng trắng ", ".
        // Các case đặc biệt: Nếu tất cả các trường đều trống, trả về chuỗi rỗng.
        $address1 = trim((string) Arr::get($billing, 'address_1', ''));
        $city = trim((string) Arr::get($billing, 'city', ''));
        $state = trim((string) Arr::get($billing, 'state', ''));
        $addressParts = array_filter([$address1, $city, $state], fn($value) => $value !== '');
        $fullAddress = implode(', ', $addressParts);

        $metaData = collect(Arr::get($payload, 'meta_data', []));
        $getMeta = fn($key) => $metaData->firstWhere('key', $key)['value'] ?? null;

        $products = [];
        $lineItems = Arr::get($payload, 'line_items', []);
        $descriptionParts = [];

        // Mục đích: Tích lũy danh sách service_type và thông tin service_products tương ứng từ các sản phẩm
        // Logic xử lý chính: 
        // - Với mỗi sản phẩm, trích xuất SKU sạch bằng cách lấy phần trước dấu |
        // - So khớp với danh sách $SERVICES_LIST để lấy service_type tương ứng
        $serviceTypes = [];
        $serviceProducts = [];

        foreach ($lineItems as $item) {
            $products[] = [
                'name' => Arr::get($item, 'name'),
                'price' => Arr::get($item, 'price'),
            ];

            // Tách SKU lấy giá trị trước ký tự | (Ví dụ: GS_LIVE_HUB|dailylive20p -> GS_LIVE_HUB)
            $rawSku = trim((string) Arr::get($item, 'sku', ''));
            $cleanSku = str_contains($rawSku, '|') ? explode('|', $rawSku)[0] : $rawSku;
            $cleanSku = trim($cleanSku);

            // Tìm thông tin dịch vụ tương ứng
            $matchedService = null;
            foreach (self::$SERVICES_LIST as $service) {
                if ($service['value'] === $cleanSku) {
                    $matchedService = $service;
                    break;
                }
            }

            if ($matchedService) {
                $serviceTypes[] = $matchedService['service_type'];
                $serviceProducts[] = [
                    'value' => $matchedService['value'],
                    'name' => $matchedService['name'],
                    'service_type' => $matchedService['service_type']
                ];
            }

            // Lấy meta data của sản phẩm
            $itemMeta = collect(Arr::get($item, 'meta_data', []));
            $loai = $itemMeta->firstWhere('key', 'pa_loai')['display_value'] ?? '';
            $soPhien = $itemMeta->firstWhere('key', 'pa_so-phien')['display_value'] ?? '';

            // Format thông tin từng sản phẩm theo mẫu
            $itemDesc = [
                Arr::get($item, 'name') . ' × ' . Arr::get($item, 'quantity'),
                "Loại: " . $loai,
                "Số phiên: " . $soPhien,
                "SKU: " . $rawSku,
                "Giá: " . number_format((float)Arr::get($item, 'price', 0), 0, '.', '.') . " VND",
                "Tổng cộng: " . number_format((float)Arr::get($item, 'total', 0), 0, '.', '.') . " VND",
            ];
            $descriptionParts[] = implode("\n", array_filter($itemDesc));
        }

        // Lọc trùng danh sách các service_type
        $serviceTypes = array_values(array_unique($serviceTypes));

        // Lọc trùng danh sách các service_products dựa trên mã 'value'
        $uniqueServiceProducts = [];
        $seenValues = [];
        foreach ($serviceProducts as $sp) {
            if (!in_array($sp['value'], $seenValues, true)) {
                $seenValues[] = $sp['value'];
                $uniqueServiceProducts[] = $sp;
            }
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

        // Mục đích: Ghép tên các sản phẩm dịch vụ trong đơn hàng cách nhau bằng dấu phẩy.
        // Logic xử lý chính: Trích xuất trường 'name' từ danh sách line_items và nối lại.
        $serviceName = implode(', ', array_filter(array_column($lineItems, 'name')));

        return self::clean([
            'request_name' => 'Đơn hàng WooCommerce #' . Arr::get($payload, 'id') .  ' - ' . Arr::get($billing, 'company'),
            'order_id' => (string) Arr::get($payload, 'id'),
            // Sử dụng biến contactName đã được xác định ở trên (ưu tiên last_name, fallback first_name)
            'contact_name' => $contactName,
            'contact_email' => Arr::get($billing, 'email'),
            'contact_phone' => Arr::get($billing, 'phone'),
            'company_name' => Arr::get($billing, 'company'),
            'tax_code' => $metaData->firstWhere('key', '_billing_at_tax_id')['value'] ?? null,
            'address' => $fullAddress,
            'account_number' => '', // STK TT
            'service_name' => $serviceName, // Tên dịch vụ trong HD
            'total_amount' => (float) Arr::get($payload, 'total', 0),
            'products' => $products,
            'description' => trim($description),

            // Các trường bổ sung thông tin sản phẩm dịch vụ tra cứu theo SKU
            'service_type' => $serviceTypes,
            'service_products' => $uniqueServiceProducts,

            // Một số trường metadata bổ sung
            'payment_account' => Arr::get($payload, 'payment_method_title'),
            'representative' => '', //Đỗ Hữu Hưng
            'representative_b' => $metaData->firstWhere('key', '_billing_at_representative')['value'] ?? null, // Đại diện bên b
            'sale' => [
                'name' => $firstName !== '' ? $firstName : $lastName,
                'phone' => trim((string) Arr::get($billing, 'phone', '')),
                'email' => trim((string) Arr::get($billing, 'email', '')),
            ],
            'link_website' => 'https://biz.accesstrade.vn',
            'note' => Arr::get($payload, 'customer_note'),
        ]);
    }

    /**
     * Đảm bảo dữ liệu là UTF-8 hợp lệ để tránh lỗi json_encode
     */
    private static function clean(mixed $data): mixed
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::clean($value);
            }
        } elseif (is_string($data)) {
            // Loại bỏ các ký tự không hợp lệ cho UTF-8
            return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        }

        return $data;
    }
}
