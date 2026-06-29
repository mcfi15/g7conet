<?php

use Illuminate\Support\Facades\Route;
use Modules\QRCode\App\Http\Controllers\QRCodeController;

// Public routes (frontend)
Route::name('qrcode.')->prefix('qr-code')->group(function () {
    Route::get('/', [QRCodeController::class, 'publicCreate'])->name('create');
    Route::post('/', [QRCodeController::class, 'store'])->name('store');
    Route::get('/{id}', [QRCodeController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [QRCodeController::class, 'editOwn'])->name('edit');
    Route::put('/{id}', [QRCodeController::class, 'updateOwn'])->name('update');
});

// Authenticated user routes
Route::name('user.')->prefix('user')->middleware(['auth:web'])->group(function () {
    Route::get('/qr-codes', [QRCodeController::class, 'userQRCodes'])->name('qr-codes');
});

// Admin routes
Route::group(['as' => 'admin.', 'prefix' => config('admin.prefix'), 'middleware' => ['auth:admin']], function () {
    Route::get('/qr-codes', [QRCodeController::class, 'index'])->name('qrcode.index');
    Route::delete('/qr-code/{id}', [QRCodeController::class, 'destroy'])->name('qrcode.destroy');
    Route::post('/qr-code/status/{id}', [QRCodeController::class, 'toggleStatus'])->name('qrcode.toggle-status');
    Route::post('/qr-code/feature/toggle', [QRCodeController::class, 'toggleFeature'])->name('qrcode.toggle-feature');
    Route::post('/qr-code/limit/save', [QRCodeController::class, 'saveDailyLimit'])->name('qrcode.save-limit');
});
