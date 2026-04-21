<?php

return [
    'name' => 'Webhook',

    // Forwarder: day la module trung gian nhan webhook va "ban di" (fan-out) sang noi khac.
    'forwarder' => [
        // Gioi han de luu log body (tranh DB phinh).
        'max_request_log_bytes' => 50_000,
        'max_response_log_bytes' => 50_000,
    ],

    // HMAC signature: cho phep lech thoi gian (timestamp) toi da (giay).
    // Muc dich: chong replay va request "tre" qua muc.
    'hmac' => [
        'max_skew_seconds' => 300,
        // Header quy uoc:
        // - X-Webhook-Timestamp: unix seconds
        // - X-Webhook-Signature: sha256=<hex>
        'headers' => [
            'timestamp' => 'X-Webhook-Timestamp',
            'signature' => 'X-Webhook-Signature',
        ],
    ],
];
