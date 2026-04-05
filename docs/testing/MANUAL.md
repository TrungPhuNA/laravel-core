# Manual Testing

Muc tieu: ghi ro cach test thu cong (curl/Postman) va expected outputs.

## Auth

- Doc: `docs/api/AUTH.md`

## RBAC (roles/permissions)

- Doc: `docs/api/RBAC.md`
- Admin UI (SPA): `/admin/settings` (dán Sanctum token vào header trên cùng)

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
