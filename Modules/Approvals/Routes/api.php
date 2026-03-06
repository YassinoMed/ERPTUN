<?php

use Illuminate\Support\Facades\Route;
use Modules\Approvals\Http\Controllers\ApprovalsApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('approvals/flows', [ApprovalsApiController::class, 'flows']);
    Route::get('approvals/requests', [ApprovalsApiController::class, 'requests']);
    Route::get('approvals/actions', [ApprovalsApiController::class, 'actions']);
});
