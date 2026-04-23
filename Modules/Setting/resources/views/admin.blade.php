<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Setting Admin</title>
    <link rel="icon" type="image/png" href="/favicon.png">

    <script>
        // Cấu hình cho SPA (không phụ thuộc env build).
        // - apiBase: base path API (đang mount dưới /api/v1)
        // - moduleBase: prefix cố định của module Setting admin (SPA).
        window.__SETTING_ADMIN__ = {
            apiBase: '/api/v1',
            moduleBase: '/admin/settings',
        };

        // Enforce login for /admin/*
        try {
            const token = (localStorage.getItem('core_api_token') ?? '').trim();
            if (!token) window.location.href = '/auth/login';
        } catch {
            // ignore
        }
    </script>

    @viteReactRefresh
    @vite([
        'Modules/Setting/resources/frontend/admin/index.css',
        'Modules/Setting/resources/frontend/admin/main.tsx',
    ])
</head>
<body>
    <div id="setting-admin-root"></div>
</body>
</html>
