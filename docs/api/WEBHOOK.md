# API: Webhook

Module Webhook cho phép mỗi user tạo nhiều "kênh webhook" riêng (mỗi kênh có `public_id` để nhận request từ bên ngoài).

Đặc điểm:

- Receiver public hỗ trợ `GET` và/hoặc `POST` (tuỳ cấu hình từng webhook).
- Có thể cấu hình auth hoặc không.
- `auth_type=none`: không cần token
- `auth_type=token`: yêu cầu `X-Webhook-Token` hoặc query `token=...`
- `auth_type=hmac`: yêu cầu chữ ký HMAC qua header `X-Webhook-Timestamp` + `X-Webhook-Signature`
- Có thể cấu hình validate params bằng `validation_rules` (Laravel validation rules).
- Table trong DB có prefix `wh_` để dễ nhận biết luồng webhook.

## Endpoint

### Hệ thống

- `GET /api/v1/webhooks/health` (kiểm tra module hoạt động)

### Quản lý webhook (cần đăng nhập)

- `GET /api/v1/webhooks` (paginate, filter/sort)
- `POST /api/v1/webhooks` (tạo webhook)
- `GET /api/v1/webhooks/{id}` (chi tiết)
- `PUT /api/v1/webhooks/{id}` (cập nhật)
- `DELETE /api/v1/webhooks/{id}` (xoá)
- `POST /api/v1/webhooks/{id}/rotate-token` (tạo token mới)
- `POST /api/v1/webhooks/{id}/rotate-secret` (tạo secret mới cho HMAC)

### Logs (cần đăng nhập)

- `GET /api/v1/webhooks/{id}/requests` (log list)
- `GET /api/v1/webhooks/{id}/requests/{requestId}` (log detail)
- `POST /api/v1/webhooks/{id}/requests/prune` (xoá log cũ)

### Receiver (public)

- `GET|POST /api/v1/webhooks/receive/{publicId}`

## Curl mẫu

Base URL nên lấy theo env của bạn (ví dụ `.env` dùng `APP_URL`).

```bash
curl --location "${APP_URL}/api/v1/webhooks/health" \
  --header 'Accept: application/json' \
  --header 'X-Locale: vi'
```

### Tạo webhook (auth_type=token)

```bash
curl --location "${APP_URL}/api/v1/webhooks" \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --header "Authorization: Bearer ${TOKEN}" \
  --data '{
    "name": "Webhook Payment Provider",
    "auth_type": "token",
    "allowed_methods": ["POST"],
    "validation_rules": {
      "email": "required|email",
      "amount": "nullable|numeric"
    },
    "description": "Nhận callback thanh toán"
  }'
```

Response sẽ trả:

- `data.webhook.public_id`
- `data.receive_url`
- `data.auth_token` (plain token, chỉ trả 1 lần)

### Receiver (POST + token)

```bash
curl --location "${APP_URL}/api/v1/webhooks/receive/${PUBLIC_ID}" \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --header "X-Webhook-Token: ${WEBHOOK_TOKEN}" \
  --data '{
    "email": "a@b.com",
    "amount": 120000
  }'
```

### Receiver (GET + token query)

```bash
curl --location "${APP_URL}/api/v1/webhooks/receive/${PUBLIC_ID}?token=${WEBHOOK_TOKEN}&email=a%40b.com"
```

### Receiver (POST + HMAC signature)

Quy ước header:

- `X-Webhook-Timestamp`: unix seconds
- `X-Webhook-Signature`: `sha256=<hex>`

Canonical string để ký:

```
{timestamp}\n{METHOD}\n{PATH}\n{QUERY}\n{BODY}
```

Ví dụ ký với `openssl` (macOS):

```bash
TS=$(date +%s)
METHOD="POST"
PATH="/api/v1/webhooks/receive/${PUBLIC_ID}"
QUERY=""
BODY='{"email":"a@b.com","amount":120000}'

CANONICAL="${TS}\n${METHOD}\n${PATH}\n${QUERY}\n${BODY}"

SIG=$(printf "%b" "$CANONICAL" | openssl dgst -sha256 -hmac "${WEBHOOK_SECRET}" -hex | awk '{print $2}')

curl --location "${APP_URL}${PATH}" \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --header "X-Webhook-Timestamp: ${TS}" \
  --header "X-Webhook-Signature: sha256=${SIG}" \
  --data "${BODY}"
```

Lưu ý:

- Server sẽ reject nếu timestamp lệch quá cấu hình `webhook.hmac.max_skew_seconds` (mặc định 300s).
- `WEBHOOK_SECRET` chỉ trả về 1 lần khi tạo/rotate secret.

## Ghi chú kiến trúc

- `Http/*`: Controller/Request/Resource (API boundary).
- `Application/*`: Use-case/Service, orchestration, transaction.
- `Domain/*`: Model, business rules (nếu có).
- `Infrastructure/*`: Repository, cache, client gọi microservice, DB adapter.

## DB Tables (prefix `wh_`)

- `wh_webhooks`: bảng cấu hình kênh webhook của user
- `wh_webhook_requests`: log request nhận vào (debug)

## Checklist phát triển (Webhook)

- [x] Tạo module `Webhook` theo convention (nwidart)
- [x] Migration `wh_webhooks` (cấu hình kênh)
- [x] Migration `wh_webhook_requests` (log request nhận vào)
- [x] API quản lý webhook (list/create/show/update/delete)
- [x] API rotate token
- [x] Receiver public `GET|POST /receive/{publicId}`
- [x] Hỗ trợ `auth_type=none|token`
- [x] Hỗ trợ validate params bằng `validation_rules`
- [x] Log request (mask header nhạy cảm)
- [x] Feature tests cơ bản (create/receive/auth/method/validation)
- [x] API quản lý log requests (list/show) + prune
- [x] Hỗ trợ chữ ký HMAC / signature (để bên thứ 3 verify tốt hơn token)
- [ ] Idempotency/dedupe (tuỳ use-case)

## TODO (mở rộng)

- API quản lý log requests (list/show) + prune
- Hỗ trợ auth kiểu khác (basic/hmac signature)
- Idempotency key / dedupe (tuỳ use-case)
