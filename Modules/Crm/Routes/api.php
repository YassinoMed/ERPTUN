<?php

use Illuminate\Support\Facades\Route;
use Modules\Crm\Http\Controllers\CrmApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('customers', [CrmApiController::class, 'customers']);
    Route::get('customers/{customer}', [CrmApiController::class, 'customerShow']);
    Route::get('products', [CrmApiController::class, 'products']);
    Route::get('products/{productService}', [CrmApiController::class, 'productShow']);
});
