<?php
// routes/web.php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public landing page
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Authentication routes (already provided by Laravel Breeze)
require __DIR__.'/auth.php';

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard route (comes with Breeze)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Role management routes (accessible to any authenticated user)
    Route::resource('roles', RoleController::class);

    // User management routes (accessible only to administrators)
    Route::middleware(['role:Administrator'])->group(function () {
        Route::resource('users', UserController::class);
    });
});