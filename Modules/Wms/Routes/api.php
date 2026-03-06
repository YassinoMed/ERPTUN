<?php

use Illuminate\Support\Facades\Route;
use Modules\Wms\Http\Controllers\WmsApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('wms/warehouses', [WmsApiController::class, 'warehouses']);
    Route::get('wms/warehouses/{warehouse}', [WmsApiController::class, 'warehouseShow']);
    Route::get('wms/warehouse-products', [WmsApiController::class, 'warehouseProducts']);
    Route::get('wms/warehouse-products/{warehouseProduct}', [WmsApiController::class, 'warehouseProductShow']);
    Route::get('wms/stock-reports', [WmsApiController::class, 'stockReports']);
});
