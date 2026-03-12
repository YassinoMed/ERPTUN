<?php

namespace Modules\KnowledgeBase\Providers;

use Illuminate\Support\ServiceProvider;

class KnowledgeBaseServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'KnowledgeBase';
    protected string $moduleNameLower = 'knowledgebase';

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
