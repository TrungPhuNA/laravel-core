<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Auth</title>

    <script>
        // Cấu hình SPA Auth
        window.__AUTH_APP__ = {
            apiBase: '/api/v1',
            moduleBase: '/auth',
        };
    </script>

    @viteReactRefresh
    @vite([
        'Modules/Auth/resources/frontend/app/index.css',
        'Modules/Auth/resources/frontend/app/main.tsx',
    ])
</head>
<body>
    <div id="auth-app-root"></div>
</body>
</html>

