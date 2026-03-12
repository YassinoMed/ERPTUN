<?php

namespace Modules\FieldService\Providers;

use Illuminate\Support\ServiceProvider;

class FieldServiceServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'FieldService';
    protected string $moduleNameLower = 'fieldservice';

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
