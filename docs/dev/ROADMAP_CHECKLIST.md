# Roadmap Checklist (Core Template)

File này dùng để theo dõi tiến độ build core-template. Tick vào checkbox khi hoàn thành.

## Nền tảng API (Core)

- [x] Chuẩn response JSend (success/fail/error)
- [x] Render lỗi API luôn là JSON cho `/api/*` (tránh HTML)
- [x] Locale theo request (`vi/en`) qua `X-Locale` / `Accept-Language` / `?lang=`
- [x] Middleware ép `Accept: application/json` cho API
- [x] `user_type` + middleware `RequireUserType`
- [x] Trace id: middleware `RequestId` + trả `X-Request-Id`
- [x] Response có `trace_id`
- [ ] Registry error code theo nhóm (AUTH_, RESOURCE_, MICROSERVICE_, QUEUE_, ...)

## Modules mẫu (để chứng minh kiến trúc)

- [x] Module Auth (register/login/me/profile/logout) + Sanctum
- [x] Module Setting (public/admin, upsert, get by key) + cache
- [x] Module User (quản trị tài khoản: list/show/create/update/user_type/password/soft delete/restore)
- [x] Quản lý queue trong module Setting (jobs/failed_jobs/batches)
- [x] Module Webhook (user tạo nhiều kênh, receiver public GET/POST, auth token tuỳ chọn, validate params)
- [ ] Module mẫu CRUD (Catalog: categories/products) để demo filter/sort/include/paginate

## Document (Scribe)

- [x] Tích hợp Scribe (docs UI + OpenAPI + Postman)
- [x] Document tiếng Việt có dấu
- [x] Group/subgroup theo chức năng
- [ ] Quy ước viết docblock chuẩn cho mọi module mới

## Generator

- [x] `core:make-module` scaffold module theo convention
- [ ] `core:make-module` auto tạo controller/request/resource/service/repo/interface và bind ServiceProvider
- [ ] `core:make-endpoint` tạo nhanh endpoint trong module có sẵn

## CRUD Toolkit (dùng lại mọi dự án)

- [x] Parse query params (`filter/sort/include/page/per_page`)
- [x] Apply query cho Eloquent (allow-list filters/sorts/includes)
- [x] Helper pagination meta (`meta.pagination`)
- [x] `ApiResponse::paginated()` trả list + meta pagination
- [x] Base Repository/CachedRepository pattern dùng chung
- [ ] Bộ rule/validator dùng chung (date range, enum, ...)

## Microservice Integration

- [x] `CoreHttpClient` (timeout/retry/header chuẩn/log mask)
- [x] Map lỗi microservice -> `ApiException` + `ErrorCode`
- [ ] Fake client pattern chuẩn cho test (gợi ý scaffold trong generator)
- [ ] Circuit breaker (tuỳ nhu cầu)

## Queue (có job và event)

- [ ] Chuẩn base Job (retry/backoff/timeout/idempotency)
- [ ] Failed job handling + log context (trace id)
- [ ] Outbox pattern (1 DB) để publish event an toàn sau transaction
- [ ] Convention event name + schema versioning

## Testing

- [x] Test format lỗi API (MethodNotAllowed trả JSON)
- [x] Test endpoints Setting
- [x] Test `CoreHttpClient` (Http::fake)
- [ ] Test CRUD toolkit (filter/sort/include/paginate) bằng model mẫu
- [ ] Test localization vi/en cho validation messages
