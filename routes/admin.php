<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CarsController;
use App\Http\Controllers\Admin\ReservationsController;
use App\Http\Controllers\Admin\ClientsController;
use App\Http\Controllers\Admin\PaymentsController;
use App\Http\Controllers\Admin\ReportsController;

Route::middleware(['auth', 'verified', 'active', 'admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        // Redirect '/admin' to '/admin/cars' with a named route we can reference
        Route::redirect('/', '/admin/cars')->name('home');
        Route::resource('cars', CarsController::class)->except(['show']);
        Route::resource('reservations', ReservationsController::class)->except(['show']);
        Route::resource('clients', ClientsController::class)->except(['show']);
        Route::resource('payments', PaymentsController::class)->except(['show']);
        Route::resource('reports', ReportsController::class)->except(['show']);
    });
