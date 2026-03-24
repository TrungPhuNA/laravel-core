# RBAC (Roles/Permissions)

Core dùng `spatie/laravel-permission` để quản lý:
- `roles`
- `permissions`
- gán roles/permissions cho `users`

## Super admin (bypass permission check)

Mặc định 2 email sau sẽ **bỏ qua check permission** (vẫn yêu cầu `auth:sanctum` và `user_type:ADMIN|SYSTEM` ở route group):
- `admin@gmail.com`
- `codethue94@gmail.com`

Có thể override bằng env:
- `CORE_RBAC_SUPER_ADMIN_EMAILS="admin@gmail.com,codethue94@gmail.com"`

## Guard

RBAC dùng guard (mặc định): `sanctum`
- env: `CORE_RBAC_GUARD=sanctum`

## Seed dữ liệu RBAC mặc định

Seeder: `Modules/Setting/Database/Seeders/RbacSeeder.php`

Tạo permissions mặc định:
- `setting.users.*`
- `setting.roles.*`
- `setting.permissions.*`

Và tạo role `Admin` (gán toàn bộ permissions trên).

## Table prefix

RBAC tables được custom prefix `alc_` trong `config/permission.php`:
- `alc_roles`, `alc_permissions`
- `alc_model_has_roles`, `alc_model_has_permissions`
- `alc_role_has_permissions`

## API endpoints (v1)

Base: `/api/v1/settings/rbac/*` (yêu cầu `auth:sanctum` + `user_type:ADMIN,SYSTEM`)

### Roles
- `GET /api/v1/settings/rbac/roles`
- `POST /api/v1/settings/rbac/roles`
- `GET /api/v1/settings/rbac/roles/{id}`
- `PUT /api/v1/settings/rbac/roles/{id}`
- `DELETE /api/v1/settings/rbac/roles/{id}`

### Permissions
- `GET /api/v1/settings/rbac/permissions`
- `POST /api/v1/settings/rbac/permissions`

### User RBAC
- `GET /api/v1/settings/rbac/users/{id}`
- `PUT /api/v1/settings/rbac/users/{id}/roles`
- `PUT /api/v1/settings/rbac/users/{id}/permissions`
