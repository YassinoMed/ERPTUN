<?php

use Illuminate\Support\Facades\Route;
use Modules\LoyaltyRewards\Http\Controllers\LoyaltyRewardsApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('loyalty-rewards/overview', [LoyaltyRewardsApiController::class, 'overview']);
});
