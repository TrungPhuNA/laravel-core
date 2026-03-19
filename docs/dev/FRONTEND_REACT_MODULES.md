# Frontend React Theo Module (Vite + Tailwind)

Mục tiêu:

- Mỗi module có một prefix URL cố định cho frontend.
- Frontend là SPA React, tự quản lý route nội bộ (React Router).
- Build/Dev dùng chung 1 Vite config ở root, nhưng mỗi module tự khai báo entrypoints.

## Yêu cầu môi trường

```bash
nvm use v20.19.0
node -v
npm -v
```

Cài dependencies:

```bash
npm install
```

Chạy dev:

```bash
npm run dev
```

## Cách khai báo entrypoints cho module

Mỗi module tạo file `Modules/{Module}/vite.paths.json` theo format:

```json
{
  "paths": [
    "Modules/Setting/resources/frontend/admin/index.css",
    "Modules/Setting/resources/frontend/admin/main.tsx"
  ]
}
```

Root Vite sẽ tự đọc `modules_statuses.json` và tự thêm entrypoints của các module đang enable.

File liên quan:

- `vite.config.js`
- `vite-module-loader.js`

## Pattern route frontend theo prefix cố định

Ví dụ module Setting:

- Prefix web: `/admin/settings`
- Laravel web route: catch-all trả về Blade view của SPA
- React Router set `basename="/admin/settings"`

Files:

- `Modules/Setting/routes/web.php`
- `Modules/Setting/resources/views/admin.blade.php`
- `Modules/Setting/resources/frontend/admin/*`

Ví dụ module Webhook:

- Prefix web: `/webhook`
- Laravel web route: catch-all trả về Blade view của SPA
- React Router set `basename="/webhook"`

Files:

- `Modules/Webhook/routes/web.php`
- `Modules/Webhook/resources/views/app.blade.php`
- `Modules/Webhook/resources/frontend/app/*`

Ví dụ module Auth (login/register):

- Prefix web: `/auth`
- React Router set `basename="/auth"`
- Token được lưu vào `localStorage` key `core_api_token` để module khác tự dùng.

Files:

- `Modules/Auth/routes/web.php`
- `Modules/Auth/resources/views/app.blade.php`
- `Modules/Auth/resources/frontend/app/*`

Ghi chú auth:

- Hiện tại template chưa tích hợp web login/session (route `login`), nên trang SPA không đặt dưới middleware `auth`.
- SPA gọi API admin bằng Bearer token (Sanctum) qua các endpoint `/api/*`.

## Tailwind

Trong module: `index.css` dùng:

```css
@import "tailwindcss";
```

Tailwind plugin ở root Vite sẽ xử lý cho mọi entry.

## Shared UI/Core (Dùng Chung Toàn Project)

Tailwind chỉ là utility CSS, không tự có sẵn Button/Modal/Table/Pagination. Để tránh mỗi module phải copy/paste UI primitives, project có thư mục dùng chung:

- `resources/frontend/shared/ui/*` (Button, Input, Modal, Pagination, ...)
- `resources/frontend/shared/http/*` (types JSend, buildQuery, createApiClient, ...)
- `resources/frontend/shared/lib/*` (format helpers)

Root Vite đã khai báo alias:

- `@shared/*` => `resources/frontend/shared/*`

Ví dụ import:

```ts
import Button from "@shared/ui/Button";
import { buildQuery } from "@shared/http/query";
```

Lưu ý: Mỗi module có thể giữ `api.ts` riêng để cấu hình baseURL/token storage theo module (ví dụ Setting dùng `window.__SETTING_ADMIN__` + `localStorage`).

## Fix lỗi "báo đỏ" trong VSCode (TypeScript/JSX)

Hiện tượng thường gặp:

- `Could not find a declaration file for module 'react/jsx-runtime'`
- `JSX element implicitly has type 'any' because no interface 'JSX.IntrinsicElements' exists`

Nguyên nhân: VSCode TypeScript server cần `typescript` + `@types/react` + `@types/react-dom` và `tsconfig.json` để hiểu JSX/TSX.

Project đã có sẵn:

- `tsconfig.json` (include toàn bộ `Modules/**/resources/frontend/**/*`)
- devDependencies: `typescript`, `@types/react`, `@types/react-dom`

Nếu vẫn còn báo đỏ: mở Command Palette và chạy:

- `TypeScript: Restart TS Server`

## Cấu trúc thư mục đề xuất (Chuẩn hoá để dễ mở rộng)

Ví dụ module Setting:

```
Modules/Setting/
  routes/web.php
  resources/views/admin.blade.php
  resources/frontend/admin/
    index.css
    main.tsx
    src/
      app/
        App.tsx
        layout/
          AdminLayout.tsx
      features/
        settings/
          pages/SettingsPage.tsx
          services/settingsApi.ts
          types.ts
        queue/
          pages/QueueOverviewPage.tsx
          pages/QueueJobsPage.tsx
          pages/QueueFailedJobsPage.tsx
          pages/QueueBatchesPage.tsx
          services/queueApi.ts
          types.ts
      shared/            # module-specific (token/locale, baseURL config)
        lib/api.ts
        state/auth.ts
```

Nguyên tắc:

- `app/*`: router + layout chung cho SPA của module.
- `features/*`: code theo nghiệp vụ (settings, queue, ...).
- `shared/*` (trong module): chỉ giữ thứ phụ thuộc module (baseURL, localStorage keys, auth state...).
- `@shared/*` (root): UI primitives + http helpers dùng chung toàn project.

## Checklist tạo SPA admin cho module mới

1. Tạo web prefix cố định (ví dụ: `/admin/users`).
2. Tạo `Modules/{Module}/routes/web.php` catch-all trả về view SPA:

```php
Route::prefix('admin/users')->group(function () {
    Route::view('/{any?}', 'user::admin')->where('any', '.*');
});
```

3. Tạo Blade view (ví dụ: `Modules/{Module}/resources/views/admin.blade.php`):

```blade
<script>
  window.__USER_ADMIN__ = { apiBase: '/api/v1', moduleBase: '/admin/users' };
</script>

@viteReactRefresh
@vite([
  'Modules/User/resources/frontend/admin/index.css',
  'Modules/User/resources/frontend/admin/main.tsx',
])
```

4. Tạo entrypoints frontend trong module:

- `Modules/{Module}/resources/frontend/admin/index.css`
- `Modules/{Module}/resources/frontend/admin/main.tsx`

Trong `main.tsx` nhớ set basename:

```tsx
<BrowserRouter basename="/admin/users">
  <App />
</BrowserRouter>
```

5. Khai báo `Modules/{Module}/vite.paths.json` để root Vite build được:

```json
{
  "paths": [
    "Modules/User/resources/frontend/admin/index.css",
    "Modules/User/resources/frontend/admin/main.tsx"
  ]
}
```

6. Trong module tạo `src/shared/lib/api.ts` riêng để:

- đọc `window.__{MODULE}_ADMIN__.apiBase`
- quản lý token/locale key trong `localStorage`
- tạo axios client qua `@shared/http/apiClient`

7. Dev: `nvm use v20.19.0` + `npm run dev`.
8. Prod build: `npm run build` (asset ra `public/build/*`).

## Lưu ý quan trọng

- Project đang API-first, không có web login/session, nên web route SPA không đặt dưới middleware `auth` (tránh lỗi `Route [login] not defined`).
- Admin SPA gọi API bằng Bearer token (Sanctum). Bạn có thể lưu token vào `localStorage` qua UI hoặc tự set bằng DevTools.
