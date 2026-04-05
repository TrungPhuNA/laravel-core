<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ecommerce Admin</title>

    <script>
        // Cấu hình cho SPA (không phụ thuộc env build).
        // - apiBase: base path API (đang mount dưới /api/v1)
        // - moduleBase: prefix cố định của module Ecommerce admin (SPA).
        window.__ECOMMERCE_ADMIN__ = {
            apiBase: '/api/v1',
            moduleBase: '/admin/ecommerce',
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
        'Modules/Ecommerce/resources/frontend/admin/index.css',
        'Modules/Ecommerce/resources/frontend/admin/main.tsx',
    ])
</head>
<body>
    <div id="ecommerce-admin-root"></div>
</body>
</html>
