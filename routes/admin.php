<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CarsController;

Route::middleware(['auth', 'verified', 'active', 'admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        // Redirect '/admin' to '/admin/cars' with a named route we can reference
        Route::redirect('/', '/admin/cars')->name('home');
        Route::get('/cars', [CarsController::class, 'index'])->name('cars.index');
        Route::get('/cars/edit', [CarsController::class, 'edit'])->name('cars.edit');
    });
