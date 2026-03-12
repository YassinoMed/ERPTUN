<?php

use Illuminate\Support\Facades\Route;
use Modules\ExpenseManagement\Http\Controllers\ExpenseManagementApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('expense-management/overview', [ExpenseManagementApiController::class, 'overview']);
});
