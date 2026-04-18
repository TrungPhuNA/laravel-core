# Manual Testing

Muc tieu: ghi ro cach test thu cong (curl/Postman) va expected outputs.

## Auth

- Doc: `docs/api/AUTH.md`

## RBAC (roles/permissions)

- Doc: `docs/api/RBAC.md`
- Admin UI (SPA): `/admin/settings` (dán Sanctum token vào header trên cùng)

## Cheat sheets

- Doc: `docs/api/CHEAT_SHEETS.md`
- Admin UI (SPA): `/admin/cheat-sheets` (yêu cầu có `core_api_token`, nếu chưa có sẽ redirect `/auth/login`)
- Public UI (SPA): `/cheat-sheets`

Seeder demo data:
```bash
php artisan db:seed --class="Modules\\CheatSheet\\Database\\Seeders\\CheatSheetDatabaseSeeder"
```

Quick flow:
- Register/Login lấy token.
- Create:
  - `POST /api/v1/cheat-sheets` với `title`, `body`, `tags`.
- List + search:
  - `GET /api/v1/cheat-sheets?filters[q]=laravel&page=1&per_page=20`
  - `GET /api/v1/cheat-sheets?filters[tag]=php`
- Update:
  - `PUT /api/v1/cheat-sheets/{id}` với `tags` mới (hoặc `[]` để xoá hết tag).
- Tags autocomplete:
  - `GET /api/v1/cheat-sheets/tags?q=la&limit=20`
- Delete:
  - `DELETE /api/v1/cheat-sheets/{id}`

## Commands

```bash
php artisan migrate
php artisan serve
```

## Ecommerce (Admin UI + API)

- Seeder quyền (chạy 1 lần sau migrate):

```bash
php artisan db:seed --class="Modules\\Ecommerce\\Database\\Seeders\\EcommerceDatabaseSeeder"
```

- Seeder demo data (customers/categories/products/orders):

```bash
ECM_SEED_DEMO_DATA=1 ECM_DEMO_SEED_RESET=1 php artisan db:seed --class="Modules\\Ecommerce\\Database\\Seeders\\EcommerceDatabaseSeeder"
```

- Admin UI: `/admin/ecommerce`
  - Dán Sanctum token (ADMIN/SYSTEM) lên header.
  - Chọn shop ở dropdown (nếu có nhiều shop).
  - Test CRUD:
    - Categories: tạo/sửa/xoá
    - Products: tạo/sửa/xoá + gán nhiều categories
    - Customers: tạo/sửa/xoá
    - Orders: tạo đơn (items JSON), xem chi tiết, cập nhật status

- API sample:
  - `GET /api/v1/ecm/admin/products?include=categories`
  - `GET /api/v1/ecm/admin/orders`

## Checklist

- [ ] Register ok
- [ ] Login ok
- [ ] Me ok (bearer token)
- [ ] Update profile ok
- [ ] Logout ok
- [ ] Ecommerce admin CRUD ok
