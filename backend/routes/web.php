<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductRequestController;
use App\Http\Controllers\Marketer\RequestController as MarketerRequestController;
use App\Http\Controllers\Marketer\StockController as MarketerStockController;
use App\Http\Controllers\Warehouse\RequestController as WarehouseRequestController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->isSalesman()) {
            return redirect()->route('marketer.requests.index');
        } else {
            return redirect()->route('warehouse.requests.index');
        }
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Marketer Routes
Route::middleware(['auth', 'role:salesman'])->prefix('marketer')->name('marketer.')->group(function () {
    Route::get('/requests', [MarketerRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [MarketerRequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [MarketerRequestController::class, 'store'])->name('requests.store');
    Route::get('/requests/{id}', [MarketerRequestController::class, 'show'])->name('requests.show');
    Route::get('/requests/{id}/cancel', [MarketerRequestController::class, 'cancel'])->name('requests.cancel');
    Route::get('/requests/{id}/print', [MarketerRequestController::class, 'printInvoice'])->name('requests.print');
    
    Route::get('/stock', [MarketerStockController::class, 'index'])->name('stock');
});

// Warehouse Keeper Routes
Route::middleware(['auth', 'role:warehouse_keeper'])->prefix('warehouse')->name('warehouse.')->group(function () {
    Route::get('/requests', [WarehouseRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/{id}', [WarehouseRequestController::class, 'show'])->name('requests.show');
    Route::patch('/requests/{id}/approve', [WarehouseRequestController::class, 'approve'])->name('requests.approve');
    Route::patch('/requests/{id}/reject', [WarehouseRequestController::class, 'reject'])->name('requests.reject');
    Route::delete('/requests/{id}/cancel', [WarehouseRequestController::class, 'cancel'])->name('requests.cancel');
    Route::get('/requests/{id}/upload-document', [WarehouseRequestController::class, 'uploadDocument'])->name('requests.upload-document');
    Route::post('/requests/{id}/store-document', [WarehouseRequestController::class, 'storeDocument'])->name('requests.store-document');
});

// Legacy Routes (للتوافق المؤقت)
Route::middleware(['auth'])->group(function () {
    Route::get('/requests', function() {
        if (Auth::user()->isSalesman()) {
            return redirect()->route('marketer.requests.index');
        } else {
            return redirect()->route('warehouse.requests.index');
        }
    })->name('requests.index');
});
