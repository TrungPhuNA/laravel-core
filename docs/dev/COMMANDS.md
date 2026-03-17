# Lệnh (Artisan)

Tài liệu này tổng hợp các lệnh nội bộ để team dùng khi phát triển.

## Tạo module theo convention (nwidart)

Project dùng `nwidart/laravel-modules`. Để tránh việc tạo module xong lại phải tự tay tạo folder, route, docs, project có lệnh:

```bash
php artisan core:make-module Catalog
```

Mặc định lệnh sẽ:

- Tạo module dạng API (`module:make --api`).
- Tạo sẵn cấu trúc thư mục theo convention.
- `Application/*`: Contracts, DTO, Services.
- `Domain/*`: Models.
- `Infrastructure/*`: Contracts, Repositories.
- `Http/*`: Controllers/Requests/Resources theo `Api/V1`.
- Tạo `routes/api.php` theo chuẩn `/api/v1/{route-prefix}/...`.
- Tạo endpoint health để test nhanh: `GET /api/v1/{route-prefix}/health`.
- Tạo docs stub: `docs/api/{MODULE}.md`.
- Chạy `scripts/index-docs.sh` để cập nhật `docs/README.md`.

### Option thường dùng

```bash
# Tự đặt route-prefix
php artisan core:make-module Setting --route-prefix=settings

# Chọn version khác (v2)
php artisan core:make-module Catalog --api-version=v2

# Ghi đè scaffold (cẩn thận khi module đã có code thật)
php artisan core:make-module Catalog --force

# Không tạo docs stub
php artisan core:make-module Catalog --no-docs
```

### Lưu ý

- Lệnh chỉ scaffold "bộ khung". Khi module bắt đầu có nghiệp vụ, bạn vẫn cần tạo service/repository/interface thật.
- Bind interface trong `Modules/{Module}/app/Providers/{Module}ServiceProvider.php`.
- Viết test theo từng endpoint/flow.
