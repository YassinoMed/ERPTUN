<?php

namespace Modules\Accounting\Providers;

use Illuminate\Support\ServiceProvider;

class AccountingServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Accounting';
    protected string $moduleNameLower = 'accounting';

    public function boot(): void
    {
        $this->registerConfig();
        if (env('SERVICE_MODE') === 'accounting') {
            config(['database.default' => env('ACCOUNTING_DB_CONNECTION', env('DB_CONNECTION', 'mysql'))]);
            config(['queue.default' => env('ACCOUNTING_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'sync'))]);
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
