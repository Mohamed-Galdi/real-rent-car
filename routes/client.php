<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\ReservationsController;

Route::middleware(['auth', 'verified', 'active', 'client'])
    ->prefix('client')
    ->as('client.')
    ->group(function () {
        Route::get('/reservations', [ReservationsController::class, 'index'])->name('reservations.index');
    });
