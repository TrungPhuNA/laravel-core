# Cheat sheets API

Module: `CheatSheet`

Base path: `/api/v1/cheat-sheets`

## Auth

- Hiện tại: tất cả endpoint yêu cầu `auth:sanctum` (Bearer token).
- Sau này public: có thể tách thêm endpoint public (không cần auth) dựa trên `visibility`.

## Endpoints

## Body format

- `body` lưu dạng Markdown (text).
- Frontend admin có preview Markdown khi xem/sửa.
- Public site `/cheat-sheets` render Markdown.

### GET `/`

Danh sách cheat sheets của user hiện tại.

Query params:
- `filters[q]`: search `title`/`body` (LIKE)
- `filters[tag]`: lọc theo tag name (có thể CSV)
- `filters[visibility]`: `private|unlisted|public`
- `sort`: `id,title,created_at,updated_at,published_at` (có thể thêm `-` để desc)
- `page`, `per_page`

Response:
- `data.items`: list `cheat_sheet`
- `meta.pagination`: pagination meta

### POST `/`

Tạo cheat sheet.

Body:
- `title` (required)
- `body` (required)
- `visibility` (optional)
- `published_at` (optional)
- `tags` (optional array<string>)

### GET `/{id}`

Chi tiết cheat sheet (chỉ owner xem được).

### PUT `/{id}`

Cập nhật cheat sheet (chỉ owner).

Body (all optional):
- `title`, `body`, `visibility`, `published_at`, `tags`

### DELETE `/{id}`

Xoá cheat sheet (soft delete).

### GET `/tags`

Gợi ý tag theo user.

Query params:
- `q` (optional)
- `limit` (default 20, max 50)

### GET `/topics`

Danh sách chủ đề (tags) kèm số lượng cheat sheets theo tag.

Query params:
- `q` (optional)
- `limit` (default 50, max 100)

## Public browse

Base path: `/api/v1/public/cheat-sheets`

### GET `/`

List cheat sheets `visibility=public`.

Query params:
- `filters[q]`, `filters[tag]`, `page`, `per_page`, `sort`

### GET `/topics`

Danh sách topics (group theo tag `slug`) kèm `count`.

### GET `/{id}`

Chi tiết cheat sheet public (render Markdown ở frontend).
