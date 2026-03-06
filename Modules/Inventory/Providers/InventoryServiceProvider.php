<?php

namespace Modules\Inventory\Providers;

use Illuminate\Support\ServiceProvider;

class InventoryServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Inventory';
    protected string $moduleNameLower = 'inventory';

    public function boot(): void
    {
        $this->registerConfig();
        if (env('SERVICE_MODE') === 'inventory') {
            config(['database.default' => env('INVENTORY_DB_CONNECTION', env('DB_CONNECTION', 'mysql'))]);
            config(['queue.default' => env('INVENTORY_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'sync'))]);
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
