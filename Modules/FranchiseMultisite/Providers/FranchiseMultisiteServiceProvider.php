<?php

namespace Modules\FranchiseMultisite\Providers;

use Illuminate\Support\ServiceProvider;

class FranchiseMultisiteServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'FranchiseMultisite';
    protected string $moduleNameLower = 'franchisemultisite';

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
