<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\VenueController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'player'])->group(function (): void {
    Route::get('/pemain', [PlayerController::class, 'dashboard'])->name('player.dashboard');
    Route::get('/venues/{venue}/booking', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/venues/{venue}/booking', [BookingController::class, 'store'])->name('bookings.store');
});

Route::middleware(['auth', 'owner'])->group(function (): void {
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('owner.dashboard');
    Route::post('/bookings/{booking}/status', [OwnerDashboardController::class, 'updateBookingStatus'])->name('bookings.status');
    Route::resource('venues', VenueController::class);
});
