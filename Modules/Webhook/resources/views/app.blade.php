<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webhook</title>
    <link rel="icon" type="image/png" href="/favicon.png">

    <script>
        // Cấu hình cho SPA Webhook (không phụ thuộc env build).
        window.__WEBHOOK_APP__ = {
            apiBase: '/api/v1',
            moduleBase: '/webhook',
        };
    </script>

    @viteReactRefresh
    @vite([
        'Modules/Webhook/resources/frontend/app/index.css',
        'Modules/Webhook/resources/frontend/app/main.tsx',
    ])
</head>
<body>
    <div id="webhook-app-root"></div>
</body>
</html>

