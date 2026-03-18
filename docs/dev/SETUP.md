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

### Nếu composer đang chạy sai PHP version

Một số máy có nhiều version PHP nên `composer` có thể đang dùng PHP 7.x (sẽ lỗi vì project yêu cầu PHP >= 8.2).

Kiểm tra nhanh:

```bash
composer --version
```

Nếu thấy `PHP version 7.x`, chạy composer bằng PHP 8.2:

```bash
/opt/homebrew/opt/php@8.2/bin/php "$(which composer)" install
/opt/homebrew/opt/php@8.2/bin/php "$(which composer)" dump-autoload -o
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
