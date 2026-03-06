<?php

namespace Modules\Operations\Providers;

use Illuminate\Support\ServiceProvider;

class OperationsServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Operations';
    protected string $moduleNameLower = 'operations';

    public function boot(): void
    {
        $this->registerConfig();
        if (env('SERVICE_MODE') === 'operations') {
            config(['database.default' => env('OPERATIONS_DB_CONNECTION', env('DB_CONNECTION', 'mysql'))]);
            config(['queue.default' => env('OPERATIONS_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'sync'))]);
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
