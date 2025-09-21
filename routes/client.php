<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\ReservationsController;

Route::middleware(['auth', 'verified', 'active', 'client'])
    ->prefix('client')
    ->as('client.')
    ->group(function () {
        // Redirect '/client' to '/client/reservations' with a named route we can reference
        Route::redirect('/', '/client/reservations')->name('home');
        Route::get('/reservations', [ReservationsController::class, 'index'])->name('reservations.index');
    });
