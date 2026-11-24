<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\VenueController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth.jwt')->name('api.')->group(function (): void {
    Route::apiResource('venues', VenueController::class);
    Route::apiResource('bookings', BookingController::class);
});
