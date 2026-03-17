# Architecture

## Core

- `app/Core/Http/Responses/ApiResponse.php`: unified success/error JSON
- `app/Core/Exceptions/ApiException.php`: explicit API errors with HTTP status + details
- `bootstrap/app.php`: registers exception renderers and API route file

## Modules

Each module lives under `Modules/{Name}` and is loaded via `nwidart/laravel-modules`.

Auth module entry points:
- Routes: `Modules/Auth/routes/api.php`
- Controller: `Modules/Auth/app/Http/Controllers/Api/V1/AuthController.php`
- Service: `Modules/Auth/app/Application/Services/AuthService.php`
