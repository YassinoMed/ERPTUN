<?php

use Illuminate\Support\Facades\Route;
use Modules\RecurringBilling\Http\Controllers\RecurringBillingApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('recurring-billing/overview', [RecurringBillingApiController::class, 'overview']);
});
