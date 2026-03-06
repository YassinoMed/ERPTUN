<?php

use Illuminate\Support\Facades\Route;
use Modules\Industry\Http\Controllers\IndustryApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('industry/hotel/room-types', [IndustryApiController::class, 'hotelRoomTypes']);
    Route::get('industry/hotel/rooms', [IndustryApiController::class, 'hotelRooms']);
    Route::get('industry/hotel/reservations', [IndustryApiController::class, 'hotelReservations']);
    Route::get('industry/traceability/lots', [IndustryApiController::class, 'traceLots']);
    Route::get('industry/traceability/trace-events', [IndustryApiController::class, 'traceEvents']);
    Route::get('industry/traceability/certificates', [IndustryApiController::class, 'traceCertificates']);
    Route::get('industry/crop-planning/parcels', [IndustryApiController::class, 'cropParcels']);
    Route::get('industry/crop-planning/crop-plans', [IndustryApiController::class, 'cropPlans']);
    Route::get('industry/crop-planning/rotation-rules', [IndustryApiController::class, 'rotationRules']);
    Route::get('industry/crop-planning/weather-alerts', [IndustryApiController::class, 'weatherAlerts']);
    Route::get('industry/cooperative/cooperatives', [IndustryApiController::class, 'cooperatives']);
    Route::get('industry/cooperative/members', [IndustryApiController::class, 'cooperativeMembers']);
    Route::get('industry/cooperative/deliveries', [IndustryApiController::class, 'harvestDeliveries']);
    Route::get('industry/cooperative/distributions', [IndustryApiController::class, 'revenueDistributions']);
    Route::get('industry/cooperative/payouts', [IndustryApiController::class, 'memberPayouts']);
    Route::get('industry/hedging/contracts', [IndustryApiController::class, 'purchaseContracts']);
    Route::get('industry/hedging/positions', [IndustryApiController::class, 'hedgePositions']);
    Route::get('industry/hedging/price-indices', [IndustryApiController::class, 'priceIndices']);
});
