# API: Users (Quản trị tài khoản)

Module: `Modules/User`

Base path: `/api/v1/users`

Yêu cầu:

- `Authorization: Bearer <token>`
- User phải có `user_type` là `ADMIN` hoặc `SYSTEM`

## 1) Danh sách tài khoản

`GET /api/v1/users`

Query hỗ trợ:

- `filter[name]`
- `filter[email]`
- `filter[user_type]`
- `filter[phone]`
- `sort=id,name,email,user_type,created_at,updated_at` (thêm `-` để sort desc)
- `page`, `per_page`

Curl mẫu:

```bash
curl --location "${APP_URL}/api/v1/users?filter[name]=demo&sort=-created_at&page=1&per_page=20" \
  --header "Accept: application/json" \
  --header "X-Locale: vi" \
  --header "Authorization: Bearer ${TOKEN}"
```

## 2) Chi tiết tài khoản

`GET /api/v1/users/{id}`

```bash
curl --location "${APP_URL}/api/v1/users/1" \
  --header "Accept: application/json" \
  --header "X-Locale: vi" \
  --header "Authorization: Bearer ${TOKEN}"
```

## 3) Tạo tài khoản

`POST /api/v1/users`

```bash
curl --location "${APP_URL}/api/v1/users" \
  --header "Accept: application/json" \
  --header "X-Locale: vi" \
  --header "Authorization: Bearer ${TOKEN}" \
  --header "Content-Type: application/json" \
  --data '{
    "name": "Demo User",
    "email": "demo2@example.com",
    "password": "password123",
    "user_type": "USER"
  }'
```

## 4) Cập nhật tài khoản

`PUT /api/v1/users/{id}`

```bash
curl --location --request PUT "${APP_URL}/api/v1/users/1" \
  --header "Accept: application/json" \
  --header "X-Locale: vi" \
  --header "Authorization: Bearer ${TOKEN}" \
  --header "Content-Type: application/json" \
  --data '{
    "phone": "0900000000",
    "province": "HCM"
  }'
```

## 5) Đổi user_type

`PATCH /api/v1/users/{id}/user-type`

```bash
curl --location --request PATCH "${APP_URL}/api/v1/users/1/user-type" \
  --header "Accept: application/json" \
  --header "X-Locale: vi" \
  --header "Authorization: Bearer ${TOKEN}" \
  --header "Content-Type: application/json" \
  --data '{
    "user_type": "ADMIN"
  }'
```

## 6) Reset mật khẩu

`PATCH /api/v1/users/{id}/password`

```bash
curl --location --request PATCH "${APP_URL}/api/v1/users/1/password" \
  --header "Accept: application/json" \
  --header "X-Locale: vi" \
  --header "Authorization: Bearer ${TOKEN}" \
  --header "Content-Type: application/json" \
  --data '{
    "password": "password1234",
    "password_confirmation": "password1234"
  }'
```

## 7) Xoá (soft delete)

`DELETE /api/v1/users/{id}`

```bash
curl --location --request DELETE "${APP_URL}/api/v1/users/1" \
  --header "Accept: application/json" \
  --header "X-Locale: vi" \
  --header "Authorization: Bearer ${TOKEN}"
```

## 8) Khôi phục tài khoản đã xoá

`POST /api/v1/users/{id}/restore`

```bash
curl --location --request POST "${APP_URL}/api/v1/users/1/restore" \
  --header "Accept: application/json" \
  --header "X-Locale: vi" \
  --header "Authorization: Bearer ${TOKEN}"
```

