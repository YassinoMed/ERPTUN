<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\InventoryApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('inventory/products', [InventoryApiController::class, 'products']);
    Route::get('inventory/products/{productService}', [InventoryApiController::class, 'productShow']);
    Route::get('inventory/warehouses', [InventoryApiController::class, 'warehouses']);
    Route::get('inventory/warehouses/{warehouse}', [InventoryApiController::class, 'warehouseShow']);
    Route::get('inventory/warehouse-products', [InventoryApiController::class, 'warehouseProducts']);
    Route::get('inventory/warehouse-products/{warehouseProduct}', [InventoryApiController::class, 'warehouseProductShow']);
});
