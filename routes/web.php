<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    // Product routes
    Route::resource('products', ProductController::class);

    // Stock routes
    Route::resource('stock', StockController::class);

    // Supplier routes
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);

    // Category routes
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    
    // Optional: Add custom product routes if needed
    // Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
