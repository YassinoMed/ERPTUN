<?php

use Illuminate\Support\Facades\Route;
use Modules\WhatsAppOmnichannel\Http\Controllers\WhatsAppOmnichannelApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('whatsapp-omnichannel/overview', [WhatsAppOmnichannelApiController::class, 'overview']);
});
