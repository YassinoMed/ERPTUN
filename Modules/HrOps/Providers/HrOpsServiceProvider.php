<?php

namespace Modules\HrOps\Providers;

use Illuminate\Support\ServiceProvider;

class HrOpsServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'HrOps';
    protected string $moduleNameLower = 'hr_ops';

    public function boot(): void
    {
        $this->registerConfig();
        if (env('SERVICE_MODE') === 'hr_ops') {
            config(['database.default' => env('HR_OPS_DB_CONNECTION', env('DB_CONNECTION', 'mysql'))]);
            config(['queue.default' => env('HR_OPS_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'sync'))]);
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
