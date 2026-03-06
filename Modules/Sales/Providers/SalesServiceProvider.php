<?php

namespace Modules\Sales\Providers;

use Illuminate\Support\ServiceProvider;

class SalesServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Sales';
    protected string $moduleNameLower = 'sales';

    public function boot(): void
    {
        $this->registerConfig();
        if (env('SERVICE_MODE') === 'sales') {
            config(['database.default' => env('SALES_DB_CONNECTION', env('DB_CONNECTION', 'mysql'))]);
            config(['queue.default' => env('SALES_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'sync'))]);
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
