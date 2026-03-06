<?php

namespace Modules\Projects\Providers;

use Illuminate\Support\ServiceProvider;

class ProjectsServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Projects';
    protected string $moduleNameLower = 'projects';

    public function boot(): void
    {
        $this->registerConfig();
        if (env('SERVICE_MODE') === 'projects') {
            config(['database.default' => env('PROJECTS_DB_CONNECTION', env('DB_CONNECTION', 'mysql'))]);
            config(['queue.default' => env('PROJECTS_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'sync'))]);
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
