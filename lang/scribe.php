<?php

return [
    'labels' => [
        'search' => 'Tìm kiếm',
        'base_url' => 'Địa chỉ gốc',
    ],

    'auth' => [
        'none' => 'API này không cần xác thực.',
        'instruction' => [
            'query' => <<<'TEXT'
                Để xác thực, truyền query parameter **`:parameterName`** trong request.
                TEXT,
            'body' => <<<'TEXT'
                Để xác thực, truyền parameter **`:parameterName`** trong body request.
                TEXT,
            'query_or_body' => <<<'TEXT'
                Để xác thực, truyền parameter **`:parameterName`** trong query string hoặc body request.
                TEXT,
            'bearer' => <<<'TEXT'
                Để xác thực, gửi header **`Authorization`** với giá trị **`"Bearer :placeholder"`**.
                TEXT,
            'basic' => <<<'TEXT'
                Để xác thực, gửi header **`Authorization`** theo dạng **`"Basic {credentials}"`**.
                `{credentials}` là `username:id:password` (nối bằng dấu :), sau đó base64-encode.
                TEXT,
            'header' => <<<'TEXT'
                Để xác thực, gửi header **`:parameterName`** với giá trị **`":placeholder"`**.
                TEXT,
        ],
        'details' => <<<'TEXT'
            Tất cả endpoint cần xác thực sẽ có nhãn `requires authentication` trong tài liệu bên dưới.
            TEXT,
    ],

    'headings' => [
        'introduction' => 'Giới thiệu',
        'auth' => 'Xác thực request',
    ],

    'endpoint' => [
        'request' => 'Yêu cầu',
        'headers' => 'Header',
        'url_parameters' => 'Tham số URL',
        'body_parameters' => 'Tham số Body',
        'query_parameters' => 'Tham số Query',
        'response' => 'Phản hồi',
        'response_fields' => 'Trường phản hồi',
        'example_request' => 'Ví dụ request',
        'example_response' => 'Ví dụ response',
        'responses' => [
            'binary' => 'Dữ liệu nhị phân',
            'empty' => 'Response rỗng',
        ],
    ],

    'try_it_out' => [
        'open' => 'Thử ngay',
        'cancel' => 'Hủy',
        'send' => 'Gửi request',
        'loading' => 'Đang gửi...',
        'received_response' => 'Đã nhận response',
        'request_failed' => 'Request bị lỗi',
        'error_help' => <<<'TEXT'
            Gợi ý: Kiểm tra kết nối mạng.
            Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
            Có thể mở DevTools Console để debug.
            TEXT,
    ],

    'links' => [
        'postman' => 'Xem Postman collection',
        'openapi' => 'Xem OpenAPI spec',
    ],
];
