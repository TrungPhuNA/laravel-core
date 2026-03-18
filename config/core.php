<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Core Template Config
    |--------------------------------------------------------------------------
    |
    | Cấu hình dùng chung cho core-template (API + modules).
    |
    */

    'http' => [
        // Timeout (giây) khi gọi microservice.
        'timeout' => (float) env('CORE_HTTP_TIMEOUT', 10),

        // Timeout (giây) cho bước connect (DNS/TCP/TLS).
        'connect_timeout' => (float) env('CORE_HTTP_CONNECT_TIMEOUT', 3),

        // Retry khi gọi microservice bị lỗi tạm thời.
        'retry' => [
            'times' => (int) env('CORE_HTTP_RETRY_TIMES', 2),
            'sleep_ms' => (int) env('CORE_HTTP_RETRY_SLEEP_MS', 200),
        ],

        // Header mặc định khi gọi microservice.
        'headers' => [
            'user_agent' => env('CORE_HTTP_USER_AGENT', 'laravel-core-api'),
        ],

        // Log request/response (để debug). Cẩn thận không log dữ liệu nhạy cảm.
        'log' => [
            'enabled' => (bool) env('CORE_HTTP_LOG', true),
            'max_body' => (int) env('CORE_HTTP_LOG_MAX_BODY', 2000),
            'masked_keys' => [
                'password',
                'password_confirmation',
                'token',
                'access_token',
                'refresh_token',
                'authorization',
                'secret',
                'client_secret',
                'api_key',
            ],
        ],
    ],

    'api' => [
        'pagination' => [
            'default_per_page' => (int) env('CORE_API_DEFAULT_PER_PAGE', 20),
            'max_per_page' => (int) env('CORE_API_MAX_PER_PAGE', 100),
            'page_param' => env('CORE_API_PAGE_PARAM', 'page'),
            'per_page_param' => env('CORE_API_PER_PAGE_PARAM', 'per_page'),
        ],
    ],
];
