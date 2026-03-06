<?php

namespace Modules\Pos\Providers;

use Illuminate\Support\ServiceProvider;

class PosServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Pos';
    protected string $moduleNameLower = 'pos';

    public function boot(): void
    {
        $this->registerConfig();
        if (env('SERVICE_MODE') === 'pos') {
            config(['database.default' => env('POS_DB_CONNECTION', env('DB_CONNECTION', 'mysql'))]);
            config(['queue.default' => env('POS_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'sync'))]);
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
