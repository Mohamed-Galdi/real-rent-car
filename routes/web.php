<?php

use App\Http\Controllers\HomePagesController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomePagesController::class, 'index'])->name('home');
Route::get('/fleet', [HomePagesController::class, 'fleet'])->name('fleet');
Route::get('/fleet/{car}', [HomePagesController::class, 'show'])->name('fleet.show');
Route::get('/booking/{reservation}', [HomePagesController::class, 'confirmation'])->name('booking.confirmation');
Route::get('/contact', [HomePagesController::class, 'contact'])->name('contact');
Route::get('/about', [HomePagesController::class, 'about'])->name('about');


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/client.php';
