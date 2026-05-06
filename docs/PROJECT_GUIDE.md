# Hướng dẫn Chuẩn hóa Cấu trúc Dự án & API (Core-Template)

Tài liệu này đúc kết cấu trúc thư mục tài liệu (`docs/`) và quy trình phát triển API từ project hiện tại để có thể áp dụng nhanh chóng cho các dự án mới.

---

## 1. Cấu trúc Tài liệu Dự án (`docs/`)

Hệ thống tài liệu được chia theo mục đích sử dụng để dễ dàng quản lý và tra cứu.

### Sơ đồ thư mục:
```text
docs/
├── api/                # Tài liệu chi tiết các Endpoint API (Postman, Specs)
│   └── AUTH.md         # Ví dụ: Tài liệu về Auth API
├── architecture/       # Tài liệu về kiến trúc hệ thống
│   ├── OVERVIEW.md     # Tổng quan kiến trúc
│   └── MODULES_MOCKUP.md # Mockup hoặc hướng dẫn thiết kế Module
├── dev/                # Hướng dẫn dành cho Developer
│   └── SETUP.md        # Hướng dẫn cài đặt môi trường (Local, Staging)
├── requests/           # Quản lý yêu cầu tính năng (Features)
│   ├── BACKLOG.md      # Danh sách các task đang chờ xử lý
│   └── TEMPLATE.md     # Template khi viết yêu cầu cho một feature mới
├── testing/            # Tài liệu về kiểm thử và lỗi
│   ├── MANUAL.md       # Ghi chú test thủ công
│   ├── FLOWS.md        # Các luồng nghiệp vụ cần kiểm tra
│   └── TEMPLATE_BUG.md # Template khi báo cáo lỗi (Bug Report)
└── README.md           # File chỉ mục (Index) dẫn đến tất cả các tài liệu trên
```

---

## 2. Quy ước Xây dựng API (Module-Based)

Dự án sử dụng kiến trúc **Modular** (`nwidart/laravel-modules`). Mỗi tính năng lớn nên là một Module riêng biệt.

### Cấu trúc một Module chuẩn:
Khi tạo mới một module (ví dụ: `Payment`), cấu trúc code phải tuân thủ:

- **Routes**: `Modules/Payment/routes/api.php`
  - Định nghĩa các endpoint API.
- **Controllers**: `Modules/Payment/app/Http/Controllers/Api/V1/...`
  - Chỉ làm nhiệm vụ điều phối (nhận request, gọi Service, trả response).
- **Requests**: `Modules/Payment/app/Http/Requests/Api/V1/...`
  - Validation dữ liệu đầu vào.
- **Resources**: `Modules/Payment/app/Http/Resources/Api/V1/...`
  - Transform dữ liệu đầu ra (Format JSON).
- **Application**: `Modules/Payment/app/Application/...`
  - **Services**: Nơi chứa logic nghiệp vụ chính (Business Logic).
  - **Contracts**: Các Interfaces định nghĩa phương thức cho Service.
  - **DTOs/Mappers**: Chuyển đổi và vận chuyển dữ liệu giữa các lớp.
- **Domain**: `Modules/Payment/app/Domain/...`
  - **Models**: Các Eloquent Model đại diện cho thực thể (Entities) và quan hệ dữ liệu.
- **Infrastructure**: `Modules/Payment/app/Infrastructure/...`
  - **Repositories**: Triển khai logic lưu trữ và truy vấn dữ liệu (Eloquent, Redis, v.v.).
  - **Contracts**: Interfaces định nghĩa các phương thức cho Repository.
  - **Query**: Các class cấu hình truy vấn, lọc dữ liệu (QueryConfig).

---

## 3. Chuẩn hóa Phản hồi API (Response Format)

Tất cả các API phải sử dụng Class `App\Core\Http\Responses\ApiResponse` để đảm bảo định dạng JSON đồng nhất.

### Định dạng JSend:
- **Thành công (Success)**:
  ```json
  {
    "status": "success",
    "code": "SUCCESS",
    "message": "OK",
    "data": { ... },
    "trace_id": "uuid-string"
  }
  ```
- **Lỗi Client (Fail - 4xx)**:
  ```json
  {
    "status": "fail",
    "code": "VALIDATION_ERROR",
    "message": "Dữ liệu không hợp lệ",
    "data": { "email": ["Email đã tồn tại"] }
  }
  ```
- **Lỗi Hệ thống (Error - 5xx)**:
  ```json
  {
    "status": "error",
    "message": "Server đang bảo trì",
    "trace_id": "uuid-string"
  }
  ```

---

## 4. Quản lý Lỗi (Exceptions)

Sử dụng `App\Core\Exceptions\ApiException` kết hợp với `App\Core\Exceptions\ErrorCode` để throw lỗi một cách tường minh.

**Ví dụ:**
```php
throw new ApiException(
    message: 'Tài khoản không đủ số dư',
    errorCode: ErrorCode::INSUFFICIENT_BALANCE,
    statusCode: 400
);
```

---

## 5. Checklist khi áp dụng sang Project mới

1. [ ] Copy thư mục `docs/` và các file template vào dự án mới.
2. [ ] Cấu trúc lại thư mục `app/Core` để chứa các Base Classes (ApiResponse, ApiException, Paginator).
3. [ ] Cài đặt package `nwidart/laravel-modules` và cấu trúc theo hướng dẫn mục 2.
4. [ ] Cấu hình `bootstrap/app.php` để bắt và render Exception theo chuẩn `ApiResponse`.
5. [ ] Cập nhật file `scripts/index-docs.sh` để tự động hóa việc đánh mục lục tài liệu.
