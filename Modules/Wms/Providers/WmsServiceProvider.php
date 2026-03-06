<?php

namespace Modules\Wms\Providers;

use Illuminate\Support\ServiceProvider;

class WmsServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Wms';
    protected string $moduleNameLower = 'wms';

    public function boot(): void
    {
        $this->registerConfig();
        if (env('SERVICE_MODE') === 'wms') {
            config(['database.default' => env('WMS_DB_CONNECTION', env('DB_CONNECTION', 'mysql'))]);
            config(['queue.default' => env('WMS_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'sync'))]);
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
