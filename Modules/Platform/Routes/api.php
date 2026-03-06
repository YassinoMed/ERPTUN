<?php

use Illuminate\Support\Facades\Route;
use Modules\Platform\Http\Controllers\PlatformApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('platform/enterprise-accounting/chart-accounts', [PlatformApiController::class, 'chartAccounts']);
    Route::get('platform/enterprise-accounting/journals', [PlatformApiController::class, 'journals']);
    Route::get('platform/integrations', [PlatformApiController::class, 'integrations']);
    Route::get('platform/integrations/webhooks', [PlatformApiController::class, 'webhooks']);
    Route::get('platform/integrations/zapier-hooks', [PlatformApiController::class, 'zapierHooks']);
    Route::get('platform/chatgpt/templates', [PlatformApiController::class, 'chatgptTemplates']);
    Route::get('platform/saas/plans', [PlatformApiController::class, 'saasPlans']);
    Route::get('platform/saas/orders', [PlatformApiController::class, 'saasOrders']);
});
