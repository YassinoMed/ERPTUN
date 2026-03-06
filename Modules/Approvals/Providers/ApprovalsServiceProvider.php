<?php

namespace Modules\Approvals\Providers;

use Illuminate\Support\ServiceProvider;

class ApprovalsServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Approvals';
    protected string $moduleNameLower = 'approvals';

    public function boot(): void
    {
        $this->registerConfig();
        if (env('SERVICE_MODE') === 'approvals') {
            config(['database.default' => env('APPROVALS_DB_CONNECTION', env('DB_CONNECTION', 'mysql'))]);
            config(['queue.default' => env('APPROVALS_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'sync'))]);
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
