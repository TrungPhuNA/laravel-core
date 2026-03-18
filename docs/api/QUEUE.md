# API: Queue (trong module Setting)

Module: `Modules/Setting` (nhóm quản trị)

Base path: `/api/v1/settings/queue`

Yêu cầu:

- `Authorization: Bearer <token>`
- User phải có `user_type` là `ADMIN` hoặc `SYSTEM`

## Endpoint

- `GET /api/v1/settings/queue/stats`
- `GET /api/v1/settings/queue/jobs`
- `GET /api/v1/settings/queue/jobs/{id}`
- `GET /api/v1/settings/queue/failed-jobs`
- `GET /api/v1/settings/queue/failed-jobs/{id}`
- `POST /api/v1/settings/queue/failed-jobs/{id}/retry`
- `DELETE /api/v1/settings/queue/failed-jobs/{id}`
- `GET /api/v1/settings/queue/batches`
- `GET /api/v1/settings/queue/batches/{id}`

## Curl mẫu

```bash
curl --location "${APP_URL}/api/v1/settings/queue/stats" \
  --header "Accept: application/json" \
  --header "X-Locale: vi" \
  --header "Authorization: Bearer ${TOKEN}"
```

