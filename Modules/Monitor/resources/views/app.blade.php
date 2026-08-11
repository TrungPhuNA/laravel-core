<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monitor</title>
    <link rel="icon" type="image/png" href="/favicon.png">

    <script>
        // Cấu hình cho SPA Monitor (không phụ thuộc env build).
        window.__MONITOR_APP__ = {
            apiBase: '/api/v1',
            moduleBase: '/monitor',
        };
    </script>

    @viteReactRefresh
    @vite([
        'Modules/Monitor/resources/frontend/app/index.css',
        'Modules/Monitor/resources/frontend/app/main.tsx',
    ])
</head>
<body>
    <div id="monitor-app-root"></div>
</body>
</html>