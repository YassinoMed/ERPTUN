<?php

use Illuminate\Support\Facades\Route;
use Modules\Production\Http\Controllers\ProductionApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('production/work-centers', [ProductionApiController::class, 'workCenters']);
    Route::get('production/boms', [ProductionApiController::class, 'boms']);
    Route::get('production/orders', [ProductionApiController::class, 'orders']);
    Route::get('production/orders/{productionOrder}', [ProductionApiController::class, 'orderShow']);
});
