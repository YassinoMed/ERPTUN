<?php

use Illuminate\Support\Facades\Route;
use Modules\Hrm\Http\Controllers\HrmApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('employees', [HrmApiController::class, 'employees']);
    Route::get('employees/{employee}', [HrmApiController::class, 'employeeShow']);
});
