<?php

namespace Modules\ExpenseManagement\Providers;

use Illuminate\Support\ServiceProvider;

class ExpenseManagementServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'ExpenseManagement';
    protected string $moduleNameLower = 'expensemanagement';

    public function boot(): void
    {
        $this->registerConfig();
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
