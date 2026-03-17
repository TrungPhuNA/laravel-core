# Auth API (v1)

Base prefix: `/api/v1/auth`

## Swagger / OpenAPI

- Swagger UI: `/docs`
- OpenAPI spec: `/docs.openapi`

## Đăng ký

`POST /register`

Dữ liệu gửi:
```json
{
  "name": "Demo User",
  "email": "demo@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "device_name": "postman"
}
```

## Đăng nhập

`POST /login`

Dữ liệu gửi:
```json
{
  "email": "demo@example.com",
  "password": "password123",
  "device_name": "postman"
}
```

## Me

`GET /me` (cần `Authorization: Bearer <token>`)

## Cập nhật profile

`PUT /profile` (cần auth)

Dữ liệu gửi (partial update, fields optional):
```json
{
  "phone": "0900000000",
  "date_of_birth": "1990-01-01",
  "address_line1": "123 Street",
  "province": "HCM"
}
```

## Đăng xuất

`POST /logout` (cần auth)
