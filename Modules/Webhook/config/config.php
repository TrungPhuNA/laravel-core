<?php

return [
    'name' => 'Webhook',

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
