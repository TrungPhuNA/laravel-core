# Settings API (v1)

Base prefix: `/api/v1/settings`

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

