<?php

use Illuminate\Support\Facades\Route;
use Modules\KnowledgeBase\Http\Controllers\KnowledgeBaseApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('knowledge-base/overview', [KnowledgeBaseApiController::class, 'overview']);
});
