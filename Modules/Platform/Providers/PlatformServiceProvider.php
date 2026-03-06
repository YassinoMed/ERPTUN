<?php

namespace Modules\Platform\Providers;

use Illuminate\Support\ServiceProvider;

class PlatformServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Platform';
    protected string $moduleNameLower = 'platform';

    public function boot(): void
    {
        $this->registerConfig();
        if (env('SERVICE_MODE') === 'platform') {
            config(['database.default' => env('PLATFORM_DB_CONNECTION', env('DB_CONNECTION', 'mysql'))]);
            config(['queue.default' => env('PLATFORM_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'sync'))]);
        }
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
        );
    }
}
