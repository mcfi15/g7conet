<?php

use Illuminate\Support\Facades\Route;
use Modules\Invoice\App\Http\Controllers\InvoiceController;

// Public routes (frontend) - no auth required
Route::name('invoice.')->prefix('invoice')->group(function () {
    Route::get('/', [InvoiceController::class, 'publicCreate'])->name('create');
    Route::post('/', [InvoiceController::class, 'store'])->name('store');
    Route::get('/{id}', [InvoiceController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [InvoiceController::class, 'editOwn'])->name('edit');
    Route::put('/{id}', [InvoiceController::class, 'updateOwn'])->name('update');
});

// Authenticated user routes - must be logged in
Route::name('user.')->prefix('user')->middleware(['auth:web'])->group(function () {
    Route::get('/invoices', [InvoiceController::class, 'userInvoices'])->name('invoices');
});

// Admin routes - auth required
Route::group(['as' => 'admin.', 'prefix' => config('admin.prefix'), 'middleware' => ['auth:admin']], function () {
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoice.index');
    Route::delete('/invoice/{id}', [InvoiceController::class, 'destroy'])->name('invoice.destroy');
    Route::post('/invoice/status/{id}', [InvoiceController::class, 'toggleStatus'])->name('invoice.toggle-status');
    Route::post('/invoice/feature/toggle', [InvoiceController::class, 'toggleFeature'])->name('invoice.toggle-feature');
    Route::post('/invoice/limit/save', [InvoiceController::class, 'saveDailyLimit'])->name('invoice.save-limit');
});
