<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

/**
 * Tạo nhanh module theo convention của project (nwidart/laravel-modules).
 *
 * Mục tiêu:
 * - Không phải mỗi lần tạo module mới lại tự tay tạo folder + route + docs.
 * - Tạo sẵn "bộ khung" để team đọc lại là hiểu và build tiếp.
 */
final class CoreMakeModule extends Command
{
    protected $signature = 'core:make-module
        {name : Tên module, ví dụ: Catalog, Order, Payment}
        {--route-prefix= : Prefix URL trong /api, ví dụ: settings, orders. Mặc định: tự động suy ra từ tên module}
        {--api-version=v1 : Version API, mặc định v1}
        {--force : Ghi đè nếu module/tệp đã tồn tại}
        {--no-docs : Không tạo docs stub trong docs/api}
        {--no-health : Không tạo endpoint health cho module}
        {--no-index-docs : Không chạy scripts/index-docs.sh sau khi tạo}';

    protected $description = 'Tạo module và scaffold các thành phần cần thiết theo core-template.';

    public function handle(Filesystem $fs): int
    {
        $nameInput = (string) $this->argument('name');
        $moduleName = Str::studly($nameInput);

        if (!preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $moduleName)) {
            $this->error('Tên module không hợp lệ. Chỉ nên dùng chữ, số, và dấu gạch dưới. Ví dụ: Catalog, Order, UserProfile');
            return self::FAILURE;
        }

        $force = (bool) $this->option('force');
        $apiVersion = strtolower((string) $this->option('api-version'));
        $apiVersionClass = Str::studly($apiVersion); // v1 -> V1

        if (!preg_match('/^v\\d+$/', $apiVersion)) {
            $this->error('api-version không hợp lệ. Dùng định dạng v1, v2, ...');
            return self::FAILURE;
        }

        $routePrefix = (string) ($this->option('route-prefix') ?: $this->defaultRoutePrefix($moduleName));

        $modulePath = base_path('Modules/'.$moduleName);
        if ($fs->isDirectory($modulePath) && !$force) {
            $this->error("Module đã tồn tại: Modules/{$moduleName}. Dùng --force nếu muốn ghi đè scaffold.");
            return self::FAILURE;
        }

        // 1) Tạo module bằng nwidart. (Nếu đã tồn tại và --force: nwidart sẽ ghi đè theo config.)
        $this->info("Đang tạo module '{$moduleName}' (type: api)...");
        Artisan::call('module:make', [
            'name' => [$moduleName],
            '--api' => true,
            '--force' => $force,
        ]);
        $this->output->write(Artisan::output());

        // 2) Tạo structure theo convention của core-template.
        $dirs = [
            "{$modulePath}/app/Domain/Models",
            "{$modulePath}/app/Application/Contracts",
            "{$modulePath}/app/Application/DTO",
            "{$modulePath}/app/Application/Services",
            "{$modulePath}/app/Infrastructure/Contracts",
            "{$modulePath}/app/Infrastructure/Repositories",
            "{$modulePath}/app/Http/Controllers/Api/{$apiVersionClass}",
            "{$modulePath}/app/Http/Requests/Api/{$apiVersionClass}",
            "{$modulePath}/app/Http/Resources/Api/{$apiVersionClass}",
        ];

        foreach ($dirs as $dir) {
            $fs->ensureDirectoryExists($dir);
        }

        // 3) Route stub (idempotent: chỉ overwrite khi --force hoặc file trống).
        $routesApiPath = "{$modulePath}/routes/api.php";
        $routesStub = $this->routesStub(
            moduleName: $moduleName,
            apiVersion: $apiVersion,
            apiVersionClass: $apiVersionClass,
            routePrefix: $routePrefix,
            withHealth: !(bool) $this->option('no-health'),
        );
        $this->writeFileIfAllowed($fs, $routesApiPath, $routesStub, $force);

        // 4) Health endpoint để test nhanh module đã load route chuẩn.
        if (!(bool) $this->option('no-health')) {
            $healthControllerPath = "{$modulePath}/app/Http/Controllers/Api/{$apiVersionClass}/HealthController.php";
            $healthControllerStub = $this->healthControllerStub($moduleName, $apiVersionClass);
            $this->writeFileIfAllowed($fs, $healthControllerPath, $healthControllerStub, $force);
        }

        // 5) Docs stub (để team biết nội dung module này).
        if (!(bool) $this->option('no-docs')) {
            $docsPath = base_path('docs/api/'.strtoupper($moduleName).'.md');
            $docsStub = $this->docsStub($moduleName, $apiVersion, $routePrefix);
            $this->writeFileIfAllowed($fs, $docsPath, $docsStub, $force);
        }

        // 6) Ghi chú vào ServiceProvider (chỉ thêm comment hướng dẫn, tránh overwrite file).
        $providerPath = "{$modulePath}/app/Providers/{$moduleName}ServiceProvider.php";
        $this->tryAppendProviderHint($fs, $providerPath);

        // 7) Cập nhật docs index (docs/README.md).
        if (!(bool) $this->option('no-index-docs')) {
            $this->info('Đang cập nhật docs index (docs/README.md)...');
            Process::path(base_path())->command(['bash', 'scripts/index-docs.sh'])->run();
        }

        $this->info('Hoàn tất.');
        $this->line("Gọi thử: GET /api/{$apiVersion}/{$routePrefix}/health");

        return self::SUCCESS;
    }

    private function defaultRoutePrefix(string $moduleName): string
    {
        $base = Str::kebab($moduleName);

        // Một số module "kiểu hệ thống" thường không dùng plural.
        if (in_array($base, ['auth', 'oauth', 'sso'], true)) {
            return $base;
        }

        // Mặc định: plural để hợp với convention resource.
        return Str::plural($base);
    }

    private function writeFileIfAllowed(Filesystem $fs, string $path, string $content, bool $force): void
    {
        $fs->ensureDirectoryExists(dirname($path));

        if ($fs->exists($path)) {
            $existing = (string) $fs->get($path);
            if (!$force && trim($existing) !== '') {
                $this->warn("Bỏ qua (đã tồn tại): {$this->relativePath($path)}");
                return;
            }
        }

        $fs->put($path, $content);
        $this->line("Tạo/Cập nhật: {$this->relativePath($path)}");
    }

    private function relativePath(string $path): string
    {
        $base = rtrim(base_path(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        return str_starts_with($path, $base) ? substr($path, strlen($base)) : $path;
    }

    private function routesStub(
        string $moduleName,
        string $apiVersion,
        string $apiVersionClass,
        string $routePrefix,
        bool $withHealth,
    ): string {
        $controllerNs = "Modules\\{$moduleName}\\Http\\Controllers\\Api\\{$apiVersionClass}\\HealthController";

        $lines = [];
        $lines[] = '<?php';
        $lines[] = '';
        $lines[] = 'use Illuminate\\Support\\Facades\\Route;';

        if ($withHealth) {
            $lines[] = "use {$controllerNs};";
        }

        $lines[] = '';
        $lines[] = "Route::prefix('{$apiVersion}/{$routePrefix}')->group(function () {";

        if ($withHealth) {
            $lines[] = "    Route::get('health', [HealthController::class, 'show']);";
        } else {
            $lines[] = '    // TODO: them routes cho module tai day.';
        }

        $lines[] = '});';
        $lines[] = '';

        return implode("\n", $lines);
    }

    private function healthControllerStub(string $moduleName, string $apiVersionClass): string
    {
        $ns = "Modules\\{$moduleName}\\Http\\Controllers\\Api\\{$apiVersionClass}";

        return implode("\n", [
            '<?php',
            '',
            "namespace {$ns};",
            '',
            'use App\\Core\\Http\\Responses\\ApiResponse;',
            'use Illuminate\\Routing\\Controller;',
            '',
            '/**',
            " * @group {$moduleName}",
            ' * @subgroup Hệ thống',
            ' */',
            'final class HealthController extends Controller',
            '{',
            '    /**',
            '     * Health check',
            '     *',
            '     * API dùng để kiểm tra module đã được load route và hoạt động.',
            '     *',
            '     * @unauthenticated',
            '     */',
            '    public function show()',
            '    {',
            '        return ApiResponse::success(',
            '            data: [',
            "                'module' => '{$moduleName}',",
            "                'time' => now()->toISOString(),",
            '            ],',
            "            code: '".strtoupper($moduleName)."_HEALTH_OK',",
            "            message: 'Module hoạt động',",
            '        );',
            '    }',
            '}',
            '',
        ]);
    }

    private function docsStub(string $moduleName, string $apiVersion, string $routePrefix): string
    {
        $upper = strtoupper($moduleName);

        return implode("\n", [
            "# API: {$moduleName}",
            '',
            'Tài liệu stub cho module mới tạo. Mục tiêu là để team có chỗ ghi lại luồng nghiệp vụ và endpoint.',
            '',
            '## Endpoint',
            '',
            "- `GET /api/{$apiVersion}/{$routePrefix}/health` (kiểm tra module hoạt động)",
            '',
            '## Curl mẫu',
            '',
            'Base URL nên lấy theo env của bạn (ví dụ `.env` dùng `APP_URL`).',
            '',
            '```bash',
            "curl --location \"\${APP_URL}/api/{$apiVersion}/{$routePrefix}/health\" \\",
            "  --header 'Accept: application/json' \\",
            "  --header 'X-Locale: vi'",
            '```',
            '',
            '## Ghi chú kiến trúc',
            '',
            '- `Http/*`: Controller/Request/Resource (API boundary).',
            '- `Application/*`: Use-case/Service, orchestration, transaction.',
            '- `Domain/*`: Model, business rules (nếu có).',
            '- `Infrastructure/*`: Repository, cache, client gọi microservice, DB adapter.',
            '',
            "## TODO ({$upper})",
            '',
            '- Bổ sung routes + controller thật.',
            '- Bổ sung service/repository/interface và bind trong `Providers/*ServiceProvider.php`.',
            '- Bổ sung test.',
            '',
        ]);
    }

    private function tryAppendProviderHint(Filesystem $fs, string $providerPath): void
    {
        if (!$fs->exists($providerPath)) {
            return;
        }

        $content = (string) $fs->get($providerPath);

        // Tránh chèn lặp.
        if (str_contains($content, 'core:make-module hint')) {
            return;
        }

        // Chèn comment ngay sau dòng register RouteServiceProvider (nếu tìm thấy).
        $needle = '$this->app->register(RouteServiceProvider::class);';
        if (!str_contains($content, $needle)) {
            return;
        }

        $hint = implode("\n", [
            '',
            '        // core:make-module hint',
            '        // Dang ky cac interface -> implementation tai day (Service, Repository, Client...).',
            '        // Vi du:',
            '        // $this->app->bind(FooServiceInterface::class, FooService::class);',
            '',
        ]);

        $updated = str_replace($needle, $needle.$hint, $content);
        $fs->put($providerPath, $updated);
    }
}
