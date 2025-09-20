<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CarsController;

Route::middleware(['auth', 'verified', 'active', 'admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('/cars', [CarsController::class, 'index'])->name('cars.index');
    });
