# Auth API (v1)

Base prefix: `/api/v1/auth`

## Register

`POST /register`

Payload:
```json
{
  "name": "Demo User",
  "email": "demo@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "device_name": "postman"
}
```

## Login

`POST /login`

Payload:
```json
{
  "email": "demo@example.com",
  "password": "password123",
  "device_name": "postman"
}
```

## Me

`GET /me` (requires `Authorization: Bearer <token>`)

## Update Profile

`PUT /profile` (requires auth)

Payload (partial update, fields are optional):
```json
{
  "phone": "0900000000",
  "date_of_birth": "1990-01-01",
  "address_line1": "123 Street",
  "province": "HCM"
}
```

## Logout

`POST /logout` (requires auth)
