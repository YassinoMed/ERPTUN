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
    Route::get('platform/advanced-features', [PlatformApiController::class, 'advancedFeatures']);
    Route::get('platform/workflow-catalog', [PlatformApiController::class, 'workflowCatalog']);
    Route::get('platform/modules/{module}', [PlatformApiController::class, 'moduleDetail']);
    Route::get('platform/advanced-insights', [PlatformApiController::class, 'advancedInsights']);
    Route::get('platform/module-insights/{module}', [PlatformApiController::class, 'moduleInsights']);
    Route::post('platform/advanced-insights/refresh', [PlatformApiController::class, 'refreshAdvancedInsights']);
    Route::get('platform/module-features/{module}', [PlatformApiController::class, 'moduleFeatureStates']);
    Route::patch('platform/module-features/{module}/{featureKey}', [PlatformApiController::class, 'updateModuleFeatureState']);
    Route::post('platform/module-features/{module}/activate', [PlatformApiController::class, 'activateModuleFeatures']);
    Route::post('platform/workflow-templates/install', [PlatformApiController::class, 'installWorkflowTemplates']);
    Route::post('platform/advanced-dashboard/provision', [PlatformApiController::class, 'provisionAdvancedDashboard']);
    Route::post('platform/recommendations/{recommendationId}/apply', [PlatformApiController::class, 'applyRecommendation']);
});
