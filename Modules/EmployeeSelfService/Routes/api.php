<?php

use Illuminate\Support\Facades\Route;
use Modules\EmployeeSelfService\Http\Controllers\EmployeeSelfServiceApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('employee-self-service/overview', [EmployeeSelfServiceApiController::class, 'overview']);
});
