<?php

namespace Modules\Webhook\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\Webhook\Application\Contracts\WebhookReceiverServiceInterface;
use Modules\Webhook\Application\Contracts\WebhookServiceInterface;
use Modules\Webhook\Application\Contracts\WebhookLogServiceInterface;
use Modules\Webhook\Application\Contracts\WebhookDestinationServiceInterface;
use Modules\Webhook\Application\Contracts\WebhookDispatchLogServiceInterface;
use Modules\Webhook\Application\Contracts\WebhookForwarderServiceInterface;
use Modules\Webhook\Application\Services\WebhookDestinationService;
use Modules\Webhook\Application\Services\WebhookDispatchLogService;
use Modules\Webhook\Application\Services\WebhookForwarderService;
use Modules\Webhook\Application\Services\WebhookReceiverService;
use Modules\Webhook\Application\Services\WebhookService;
use Modules\Webhook\Application\Services\WebhookLogService;
use Modules\Webhook\Infrastructure\Contracts\WebhookDestinationRepositoryInterface;
use Modules\Webhook\Infrastructure\Contracts\WebhookDispatchLogRepositoryInterface;
use Modules\Webhook\Infrastructure\Contracts\WebhookRepositoryInterface;
use Modules\Webhook\Infrastructure\Contracts\WebhookRequestRepositoryInterface;
use Modules\Webhook\Infrastructure\Repositories\EloquentWebhookDestinationRepository;
use Modules\Webhook\Infrastructure\Repositories\EloquentWebhookDispatchLogRepository;
use Modules\Webhook\Infrastructure\Repositories\EloquentWebhookRepository;
use Modules\Webhook\Infrastructure\Repositories\EloquentWebhookRequestRepository;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class WebhookServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Webhook';

    protected string $nameLower = 'webhook';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        // Dang ky cac interface -> implementation tai day (Service, Repository, Client...).
        $this->app->bind(WebhookRepositoryInterface::class, EloquentWebhookRepository::class);
        $this->app->bind(WebhookRequestRepositoryInterface::class, EloquentWebhookRequestRepository::class);
        $this->app->bind(WebhookDestinationRepositoryInterface::class, EloquentWebhookDestinationRepository::class);
        $this->app->bind(WebhookDispatchLogRepositoryInterface::class, EloquentWebhookDispatchLogRepository::class);

        $this->app->bind(WebhookServiceInterface::class, WebhookService::class);
        $this->app->bind(WebhookReceiverServiceInterface::class, WebhookReceiverService::class);
        $this->app->bind(WebhookLogServiceInterface::class, WebhookLogService::class);
        $this->app->bind(WebhookDestinationServiceInterface::class, WebhookDestinationService::class);
        $this->app->bind(WebhookDispatchLogServiceInterface::class, WebhookDispatchLogService::class);
        $this->app->bind(WebhookForwarderServiceInterface::class, WebhookForwarderService::class);

    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
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

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $configPath = module_path($this->name, config('modules.paths.generator.config.path'));

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $config = str_replace($configPath.DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $config_key = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $config);
                    $segments = explode('.', $this->nameLower.'.'.$config_key);

                    // Remove duplicated adjacent segments
                    $normalized = [];
                    foreach ($segments as $segment) {
                        if (end($normalized) !== $segment) {
                            $normalized[] = $segment;
                        }
                    }

                    $key = ($config === 'config.php') ? $this->nameLower : implode('.', $normalized);

                    $this->publishes([$file->getPathname() => config_path($config)], 'config');
                    $this->merge_config_from($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Merge config from the given path recursively.
     */
    protected function merge_config_from(string $path, string $key): void
    {
        $existing = config($key, []);
        $module_config = require $path;

        config([$key => array_replace_recursive($existing, $module_config)]);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        Blade::componentNamespace(config('modules.namespace').'\\' . $this->name . '\\View\\Components', $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
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
