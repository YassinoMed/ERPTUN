<?php

namespace Modules\Industry\Providers;

use Illuminate\Support\ServiceProvider;

class IndustryServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Industry';
    protected string $moduleNameLower = 'industry';

    public function boot(): void
    {
        $this->registerConfig();
        if (env('SERVICE_MODE') === 'industry') {
            config(['database.default' => env('INDUSTRY_DB_CONNECTION', env('DB_CONNECTION', 'mysql'))]);
            config(['queue.default' => env('INDUSTRY_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'sync'))]);
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
