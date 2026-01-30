<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductRequestController;
use App\Http\Controllers\Marketer\RequestController as MarketerRequestController;
use App\Http\Controllers\Marketer\StockController as MarketerStockController;
use App\Http\Controllers\Marketer\Returns\ReturnController as MarketerReturnController;
use App\Http\Controllers\Marketer\Sales\SalesInvoiceController;
use App\Http\Controllers\Marketer\Promotions\PromotionController as MarketerPromotionController;
use App\Http\Controllers\Marketer\Payments\PaymentController as MarketerPaymentController;
use App\Http\Controllers\Warehouse\RequestController as WarehouseRequestController;
use App\Http\Controllers\Warehouse\Returns\ReturnController as WarehouseReturnController;
use App\Http\Controllers\Warehouse\Sales\SalesConfirmationController;
use App\Http\Controllers\Warehouse\Payments\PaymentConfirmationController;
use App\Http\Controllers\Admin\Promotions\PromotionController;
use App\Http\Controllers\Admin\Stores\StoreController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Api\DiscountCalculatorController;

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

// API Routes
Route::get('/api/calculate-discount', [DiscountCalculatorController::class, 'calculate']);

// Marketer Routes
Route::middleware(['auth', 'role:salesman'])->prefix('marketer')->name('marketer.')->group(function () {
    Route::get('/requests', [MarketerRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [MarketerRequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [MarketerRequestController::class, 'store'])->name('requests.store');
    Route::get('/requests/{id}', [MarketerRequestController::class, 'show'])->name('requests.show');
    Route::get('/requests/{id}/cancel', [MarketerRequestController::class, 'cancel'])->name('requests.cancel');
    Route::get('/requests/{id}/print', [MarketerRequestController::class, 'printInvoice'])->name('requests.print');
    
    Route::get('/stock', [MarketerStockController::class, 'index'])->name('stock');
    
    Route::get('/promotions', [MarketerPromotionController::class, 'index'])->name('promotions.index');
    
    Route::get('/returns', [MarketerReturnController::class, 'index'])->name('returns.index');
    Route::get('/returns/create', [MarketerReturnController::class, 'create'])->name('returns.create');
    Route::post('/returns', [MarketerReturnController::class, 'store'])->name('returns.store');
    Route::get('/returns/{id}', [MarketerReturnController::class, 'show'])->name('returns.show');
    Route::get('/returns/{id}/cancel', [MarketerReturnController::class, 'cancel'])->name('returns.cancel');
    Route::get('/returns/{id}/print', [MarketerReturnController::class, 'printInvoice'])->name('returns.print');
    
    // Sales Routes
    Route::get('/sales', [SalesInvoiceController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SalesInvoiceController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SalesInvoiceController::class, 'store'])->name('sales.store');
    Route::get('/sales/{id}', [SalesInvoiceController::class, 'show'])->name('sales.show');
    Route::get('/sales/{id}/print', [SalesInvoiceController::class, 'print'])->name('sales.print');
    Route::get('/sales/{id}/cancel', [SalesInvoiceController::class, 'cancel'])->name('sales.cancel');
    
    // Payment Routes
    Route::get('/payments', [MarketerPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [MarketerPaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [MarketerPaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{id}', [MarketerPaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/{id}/print', [MarketerPaymentController::class, 'print'])->name('payments.print');
    Route::get('/payments/{id}/cancel', [MarketerPaymentController::class, 'cancel'])->name('payments.cancel');
    Route::get('/payments-received', [MarketerPaymentController::class, 'received'])->name('payments.received');
    Route::get('/my-profits', [MarketerPaymentController::class, 'myProfits'])->name('my-profits');
});

// Warehouse Keeper Routes
Route::middleware(['auth', 'role:warehouse_keeper'])->prefix('warehouse')->name('warehouse.')->group(function () {
    Route::get('/requests', [WarehouseRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/{id}', [WarehouseRequestController::class, 'show'])->name('requests.show');
    Route::get('/requests/{id}/print', [WarehouseRequestController::class, 'printInvoice'])->name('requests.print');
    Route::patch('/requests/{id}/approve', [WarehouseRequestController::class, 'approve'])->name('requests.approve');
    Route::patch('/requests/{id}/reject', [WarehouseRequestController::class, 'reject'])->name('requests.reject');
    Route::patch('/requests/{id}/reject-approved', [WarehouseRequestController::class, 'rejectApproved'])->name('requests.reject-approved');
    Route::delete('/requests/{id}/cancel', [WarehouseRequestController::class, 'cancel'])->name('requests.cancel');
    Route::get('/requests/{id}/upload-document', [WarehouseRequestController::class, 'uploadDocument'])->name('requests.upload-document');
    Route::post('/requests/{id}/store-document', [WarehouseRequestController::class, 'storeDocument'])->name('requests.store-document');
    
    Route::get('/returns', [WarehouseReturnController::class, 'index'])->name('returns.index');
    Route::get('/returns/{id}', [WarehouseReturnController::class, 'show'])->name('returns.show');
    Route::get('/returns/{id}/print', [WarehouseReturnController::class, 'printInvoice'])->name('returns.print');
    Route::patch('/returns/{id}/approve', [WarehouseReturnController::class, 'approve'])->name('returns.approve');
    Route::patch('/returns/{id}/reject', [WarehouseReturnController::class, 'reject'])->name('returns.reject');
    Route::get('/returns/{id}/upload-document', [WarehouseReturnController::class, 'uploadDocument'])->name('returns.upload-document');
    Route::post('/returns/{id}/store-document', [WarehouseReturnController::class, 'storeDocument'])->name('returns.store-document');
    
    // Sales Confirmation Routes
    Route::get('/sales', [SalesConfirmationController::class, 'index'])->name('sales.index');
    Route::get('/sales/{id}', [SalesConfirmationController::class, 'show'])->name('sales.show');
    Route::post('/sales/{invoiceId}/confirm', [SalesConfirmationController::class, 'confirm'])->name('sales.confirm');
    
    // Payment Confirmation Routes
    Route::get('/payments', [PaymentConfirmationController::class, 'index'])->name('payments.index');
    Route::get('/payments/{id}', [PaymentConfirmationController::class, 'show'])->name('payments.show');
    Route::post('/payments/{paymentId}/confirm', [PaymentConfirmationController::class, 'confirm'])->name('payments.confirm');
    Route::post('/payments/{paymentId}/reject', [PaymentConfirmationController::class, 'reject'])->name('payments.reject');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
    Route::get('/promotions/create', [PromotionController::class, 'create'])->name('promotions.create');
    Route::post('/promotions', [PromotionController::class, 'store'])->name('promotions.store');
    Route::get('/promotions/{id}/edit', [PromotionController::class, 'edit'])->name('promotions.edit');
    Route::put('/promotions/{id}', [PromotionController::class, 'update'])->name('promotions.update');
    Route::post('/promotions/{id}/toggle', [PromotionController::class, 'toggleStatus'])->name('promotions.toggle');
    Route::delete('/promotions/{id}', [PromotionController::class, 'destroy'])->name('promotions.destroy');
    
    Route::post('/discount-tiers', [\App\Http\Controllers\Admin\Promotions\DiscountTierController::class, 'store'])->name('discount-tiers.store');
    Route::put('/discount-tiers/{id}', [\App\Http\Controllers\Admin\Promotions\DiscountTierController::class, 'update'])->name('discount-tiers.update');
    Route::post('/discount-tiers/{id}/toggle', [\App\Http\Controllers\Admin\Promotions\DiscountTierController::class, 'toggleStatus'])->name('discount-tiers.toggle');
    Route::delete('/discount-tiers/{id}', [\App\Http\Controllers\Admin\Promotions\DiscountTierController::class, 'destroy'])->name('discount-tiers.destroy');
    
    Route::get('/stores', [StoreController::class, 'index'])->name('stores.index');
    Route::get('/stores/{id}/ledger', [StoreController::class, 'ledger'])->name('stores.ledger');
    
    Route::get('/commissions', [\App\Http\Controllers\Admin\Commissions\CommissionController::class, 'index'])->name('commissions.index');
    Route::get('/commissions/settings', [\App\Http\Controllers\Admin\Commissions\CommissionController::class, 'settings'])->name('commissions.settings');
    Route::post('/commissions/{id}/update-rate', [\App\Http\Controllers\Admin\Commissions\CommissionController::class, 'updateRate'])->name('commissions.update-rate');
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
