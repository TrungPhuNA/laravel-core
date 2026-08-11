<?php

namespace Modules\Monitor\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Modules\Monitor\Application\Contracts\DomainMonitorServiceInterface;
use Modules\Monitor\Application\Contracts\DomainWhoisServiceInterface;
use Modules\Monitor\Application\Contracts\MonitorSettingServiceInterface;
use Modules\Monitor\Application\Services\DomainMonitorService;
use Modules\Monitor\Application\Services\DomainWhoisService;
use Modules\Monitor\Application\Services\MonitorSettingService;
use Modules\Monitor\Console\CheckDomainsCommand;
use Modules\Monitor\Domain\Models\Domain;
use Modules\Monitor\Domain\Models\DomainCheckLog;
use Modules\Monitor\Infrastructure\Contracts\DomainCheckLogRepositoryInterface;
use Modules\Monitor\Infrastructure\Contracts\DomainRepositoryInterface;
use Modules\Monitor\Infrastructure\Repositories\EloquentDomainCheckLogRepository;
use Modules\Monitor\Infrastructure\Repositories\EloquentDomainRepository;

class MonitorServiceProvider extends ServiceProvider
{
    protected string $name = 'Monitor';

    protected string $nameLower = 'monitor';

    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));

        // Merge cài đặt từ DB vào config để Domain::badge() đọc đúng ngưỡng đã chỉnh trên UI.
        if (Schema::hasTable('dmn_settings')) {
            $this->app->make(MonitorSettingServiceInterface::class)->loadIntoConfig();
        }
    }

    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

        $this->app->bind(DomainRepositoryInterface::class, EloquentDomainRepository::class);
        $this->app->bind(DomainCheckLogRepositoryInterface::class, EloquentDomainCheckLogRepository::class);

        $this->app->bind(DomainWhoisServiceInterface::class, DomainWhoisService::class);
        $this->app->bind(DomainMonitorServiceInterface::class, DomainMonitorService::class);
        $this->app->bind(MonitorSettingServiceInterface::class, MonitorSettingService::class);
    }

    protected function registerCommands(): void
    {
        $this->commands([
            CheckDomainsCommand::class,
        ]);
    }

    protected function registerCommandSchedules(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('monitor:domains:check')->dailyAt('02:00');
        });
    }

    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    protected function registerConfig(): void
    {
        $configPath = module_path($this->name, 'config/config.php');

        $this->publishes([$configPath => config_path($this->nameLower.'.php')], 'config');
        $this->mergeConfigFrom($configPath, $this->nameLower);
    }

    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);
    }

    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }
}