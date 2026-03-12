<?php

use Illuminate\Support\Facades\Route;
use Modules\CustomerSubscriptions\Http\Controllers\CustomerSubscriptionsApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('customer-subscriptions/overview', [CustomerSubscriptionsApiController::class, 'overview']);
});
