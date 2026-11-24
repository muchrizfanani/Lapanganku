<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VenueController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/venues');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'owner'])->group(function (): void {
    Route::resource('venues', VenueController::class);
});
