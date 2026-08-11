<?php

/*
|--------------------------------------------------------------------------
| Monitor module config
|--------------------------------------------------------------------------
| Cấu hình chức năng quản lý domain & thời gian hết hạn.
| Các giá trị có thể bị override bởi DB settings (bảng dmn_settings) qua
| MonitorSettingService khi user chỉnh trong màn hình Cấu hình.
*/

return [
    'name' => 'Monitor',

    // Cách check hạn domain, chạy theo thứ tự: rdap -> whois -> third_party.
    'check' => [
        'rdap' => [
            'enabled' => true,
            // Endpoint RDAP bootstrap. {domain} sẽ được thay bằng tên domain.
            'endpoint' => 'https://rdap.org/domain/{domain}',
            'timeout' => 15,
        ],

        'whois' => [
            'enabled' => true,
            'timeout' => 10,
            // Map TLD -> whois server (port 43).
            'servers' => [
                'com' => 'whois.verisign-grs.com',
                'net' => 'whois.verisign-grs.com',
                'org' => 'whois.pir.org',
                'io' => 'whois.nic.io',
                'info' => 'whois.nic.info',
                'co' => 'whois.nic.co',
                'vn' => 'whois.vnnic.vn',
                'xyz' => 'whois.nic.xyz',
                'dev' => 'whois.nic.google',
                'app' => 'whois.nic.google',
            ],
        ],

        // Fallback cuối khi RDAP/WHOIS đều fail (VD: .vn bị chặn port 43).
        'third_party' => [
            'enabled' => false,
            'provider' => 'whoisxml',
            'api_key' => env('MONITOR_WHOIS_API_KEY'),
            // Endpoint mẫu cho WhoisXMLAPI. {domain} được thay bằng tên domain.
            'endpoint' => 'https://www.whoisxmlapi.com/whoisserver/WhoisService?domainName={domain}&apiKey={apiKey}&outputFormat=JSON',
        ],
    ],

    // Ngưỡng màu cảnh báo (ngày còn lại trước khi hết hạn).
    'warning' => [
        'normal_days' => 60,    // > 60 ngày -> xanh
        'soon_days' => 30,      // 30-60 ngày -> vàng
        'critical_days' => 7,   // < 7 ngày -> cam; hết hạn -> đỏ
    ],
];