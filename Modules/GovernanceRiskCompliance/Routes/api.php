<?php

use Illuminate\Support\Facades\Route;
use Modules\GovernanceRiskCompliance\Http\Controllers\GovernanceRiskComplianceApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('grc/overview', [GovernanceRiskComplianceApiController::class, 'overview']);
});
