<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CheatSheet Admin</title>

    <script>
        // Cấu hình cho SPA (không phụ thuộc env build).
        window.__CHEATSHEET_ADMIN__ = {
            apiBase: '/api/v1',
            moduleBase: '/admin/cheat-sheets',
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
        'Modules/CheatSheet/resources/frontend/admin/index.css',
        'Modules/CheatSheet/resources/frontend/admin/main.tsx',
    ])
</head>
<body>
    <div id="cheatsheet-admin-root"></div>
</body>
</html>

