<?php
// routes/web.php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
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
    
    // Resource routes for categories
    Route::resource('categories', CategoryController::class);

    // Resource routes for books
    Route::resource('books', BookController::class);

    // Resource routes for borrowings
    Route::resource('borrowings', BorrowingController::class);

     // Export
    Route::get('/excel/export/{type}', [ExcelController::class, 'showExportForm'])->name('excel.export.form');
    Route::post('/excel/export/{type}', [ExcelController::class, 'export'])->name('excel.export');
    
    // Import
    Route::get('/excel/import/{type}', [ExcelController::class, 'showImportForm'])->name('excel.import.form');
    Route::post('/excel/import/{type}', [ExcelController::class, 'import'])->name('excel.import');
    
    // Template
    Route::get('/excel/template/{type}', [ExcelController::class, 'downloadTemplate'])->name('excel.template');
});