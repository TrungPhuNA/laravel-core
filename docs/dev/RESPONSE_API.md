# Chuẩn Response API (JSend)

Tài liệu này định nghĩa format response thống nhất cho toàn bộ API (success/fail/error) theo JSend.

## Quy ước chung

- Response body luôn là JSON và field name dùng `snake_case`.
- Tracing: client nên gửi header `X-Request-Id` (hoặc `X-Correlation-Id`) cho mỗi request. Nếu không có, server sẽ tự generate và trả lại `X-Request-Id` trong response header.
- Đa ngôn ngữ (vi/en): gửi `Accept-Language: vi|en` hoặc `X-Locale: vi|en` hoặc query `?lang=vi|en`.

## Format

### 1) Success

```json
{
  "status": "success",
  "code": "SUCCESS",
  "message": "OK",
  "data": {},
  "trace_id": "01JNV... (ví dụ)"
}
```

### 2) Fail (lỗi phía client: validation/precondition)

```json
{
  "status": "fail",
  "code": "VALIDATION_ERROR",
  "message": "Dữ liệu không hợp lệ",
  "data": {
    "email": ["The email field is required."]
  },
  "trace_id": "01JNV... (ví dụ)"
}
```

### 3) Error (lỗi phía server/exception)

```json
{
  "status": "error",
  "code": "ERROR",
  "message": "Server error",
  "data": {},
  "trace_id": "01JNV... (ví dụ)"
}
```

Ghi chú:
- `code` và `message` dùng để giải thích lỗi rõ ràng cho client.
- `data` ở `fail` thường là map lỗi theo field; ở `error` có thể để trống hoặc chứa debug info (không khuyến nghị ở production).

## HTTP status codes

Khuyến nghị:
- `200`: success
- `400`: fail (validation/precondition)
- `404`: fail (not found)
- `401/403`: fail (auth/permission)
- `500`: error

## Pagination (gợi ý)

Khi trả list:
```json
{
  "status": "success",
  "code": "SUCCESS",
  "message": "OK",
  "data": {
    "items": []
  },
  "meta": {
    "pagination": {
      "page": 1,
      "per_page": 20,
      "total": 100,
      "last_page": 5
    }
  }
}
```

## Mapping trong code

- Helper response: `app/Core/Http/Responses/ApiResponse.php`
- Exception rendering: `bootstrap/app.php`
