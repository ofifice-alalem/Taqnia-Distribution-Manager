<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductRequestController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/', function () {
    return redirect()->route('requests.index');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Product Request Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/requests', [ProductRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [ProductRequestController::class, 'create'])
        ->middleware('role:salesman')
        ->name('requests.create');
    Route::post('/requests', [ProductRequestController::class, 'store'])
        ->middleware('role:salesman')
        ->name('requests.store');
    Route::get('/requests/{id}', [ProductRequestController::class, 'show'])->name('requests.show');
    Route::patch('/requests/{id}/approve', [ProductRequestController::class, 'approve'])
        ->middleware('role:warehouse_keeper')
        ->name('requests.approve');
    Route::patch('/requests/{id}/reject', [ProductRequestController::class, 'reject'])
        ->middleware('role:warehouse_keeper')
        ->name('requests.reject');
    Route::get('/requests/{id}/upload-document', [ProductRequestController::class, 'uploadDocument'])
        ->middleware('role:warehouse_keeper')
        ->name('requests.upload-document');
    Route::post('/requests/{id}/store-document', [ProductRequestController::class, 'storeDocument'])
        ->middleware('role:warehouse_keeper')
        ->name('requests.store-document');
});
