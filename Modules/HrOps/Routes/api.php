<?php

use Illuminate\Support\Facades\Route;
use Modules\HrOps\Http\Controllers\HrOpsApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('hr-ops/employees', [HrOpsApiController::class, 'employees']);
    Route::get('hr-ops/leaves', [HrOpsApiController::class, 'leaves']);
});
