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

## Checklist

- [ ] Register ok
- [ ] Login ok
- [ ] Me ok (bearer token)
- [ ] Update profile ok
- [ ] Logout ok
