# Setup

## PHP 8.2

```bash
export PATH="/opt/homebrew/opt/php@8.2/bin:$PATH"
export PATH="/opt/homebrew/opt/php@8.2/sbin:$PATH"
php -v
```

## Cài đặt

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

## Chạy local

```bash
php artisan serve
```

## API Docs (Swagger/OpenAPI)

```bash
php artisan scribe:generate
```

Open:
- `/docs`
- `/docs.openapi`

## Domain cho curl mẫu (Scribe)

Thêm vào `.env`:
```bash
SCRIBE_BASE_URL=${APP_URL}
```
