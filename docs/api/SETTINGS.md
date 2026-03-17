# Settings API (v1)

Base prefix: `/api/v1/settings`

## Get by key

`GET /{key}`

Ghi chú:
- Nếu key là public -> không cần token.
- Nếu key không public -> cần `Authorization: Bearer <token>` và `user_type` là `ADMIN` hoặc `SYSTEM`.

## Public list

`GET /public`

## Admin list (ADMIN/SYSTEM)

`GET /`

Headers:
- `Authorization: Bearer <token>`
- `Accept-Language: vi|en` (optional)

## Upsert (ADMIN/SYSTEM)

`PUT /`

Payload:
```json
{
  "items": [
    {
      "key": "site_name",
      "value": "Core API",
      "group": "general",
      "is_public": true,
      "description": "Tên website"
    }
  ]
}
```
