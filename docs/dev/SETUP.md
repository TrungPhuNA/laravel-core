# Setup

## PHP 8.2

```bash
export PATH="/opt/homebrew/opt/php@8.2/bin:$PATH"
export PATH="/opt/homebrew/opt/php@8.2/sbin:$PATH"
php -v
```

## Install

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

## Run (local)

```bash
php artisan serve
```
