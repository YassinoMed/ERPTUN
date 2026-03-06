<?php

namespace Modules\Btp\Providers;

use Illuminate\Support\ServiceProvider;

class BtpServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Btp';
    protected string $moduleNameLower = 'btp';

    public function boot(): void
    {
        $this->registerConfig();
        if (env('SERVICE_MODE') === 'btp') {
            config(['database.default' => env('BTP_DB_CONNECTION', env('DB_CONNECTION', 'mysql'))]);
            config(['queue.default' => env('BTP_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'sync'))]);
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
