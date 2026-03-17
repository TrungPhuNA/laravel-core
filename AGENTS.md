# Agent Notes (laravel-core)

This repo is a Laravel API core scaffold using:
- PHP 8.2
- Laravel 12
- `nwidart/laravel-modules` (feature modules under `Modules/`)
- Laravel Sanctum (API tokens)

## Conventions

- New API endpoints should live inside a module:
  - Routes: `Modules/{Module}/routes/api.php` (mounted under `/api` by the module RouteServiceProvider)
  - Controllers: `Modules/{Module}/app/Http/Controllers/Api/V1/...`
  - Requests: `Modules/{Module}/app/Http/Requests/Api/V1/...`
  - Resources: `Modules/{Module}/app/Http/Resources/Api/V1/...`
  - Application services: `Modules/{Module}/app/Application/Services/...`
  - Contracts (interfaces): `Modules/{Module}/app/Application/Contracts/...`

- Global response/error format:
  - `App\Core\Http\Responses\ApiResponse`
  - `App\Core\Exceptions\ApiException`, `App\Core\Exceptions\ErrorCode`
  - Exception rendering is configured in `bootstrap/app.php`.

## Requests / Testing (where to write)

- New feature request:
  - Big: `docs/requests/REQ-YYYYMMDD-<slug>.md` (use `docs/requests/TEMPLATE.md`)
  - Small: `docs/requests/BACKLOG.md`
- Manual test notes: `docs/testing/MANUAL.md`
- Flow review notes: `docs/testing/FLOWS.md`
- Bug report: `docs/testing/BUG-YYYYMMDD-<slug>.md` (use `docs/testing/TEMPLATE_BUG.md`)

## Re-index docs

- Run: `bash scripts/index-docs.sh`

## Common commands

- Migrate: `php artisan migrate`
- List routes: `php artisan route:list`
- Run tests: `php artisan test`
- Create a module: `php artisan module:make {Name}`

## Docs

- Index: `docs/README.md`
- Setup: `docs/dev/SETUP.md`
- Auth API: `docs/api/AUTH.md`
- Architecture overview: `docs/architecture/OVERVIEW.md`
- Module mockups/examples: `docs/architecture/MODULES_MOCKUP.md`
