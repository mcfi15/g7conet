<?php

use Illuminate\Support\Facades\Route;
use Modules\Ecommerce\Http\Controllers\Admin\OrderController;
use Modules\Ecommerce\Http\Controllers\Admin\ProductController;
use Modules\Ecommerce\Http\Controllers\Admin\ShippingMethodController;
use Modules\Ecommerce\Http\Controllers\CartController;
use Modules\Ecommerce\Http\Controllers\CheckoutController;
use Modules\Ecommerce\Http\Controllers\EcommercePaymentController;
use Modules\Ecommerce\Http\Controllers\ProductController as PublicProductController;
use Modules\Ecommerce\Http\Controllers\UserOrderController;
use Modules\Ecommerce\Http\Controllers\UserPaypalController;

Route::group(['as'=> 'admin.', 'prefix' => config('admin.prefix').'/ecommerce', 'middleware' => ['MaintenanceMode','auth:admin']], function () {
    Route::prefix('product')->controller(ProductController::class)->name('product.')->group(function () {
        Route::get('/','index')->name('index');
        Route::get('create','create')->name('create');
        Route::get('edit', 'edit')->name('edit');
        Route::post('store/{id?}','store')->name('store');
        Route::put('update/{id}','update')->name('update');
        Route::put('status/{id}', 'status')->name('status');
        Route::get('gallery/{id}','gallery')->name('gallery');
        Route::delete('delete/{id}','delete')->name('delete');
        Route::post('upload-gallery/{id}', 'uploadGallery')->name('uploadGallery');
        Route::delete('delete-gallery/{id}', 'deleteGallery')->name('deleteGallery');
        Route::get('child-categories-by-sub-category/{subCategoryId}', 'getChildCategories');
        Route::get('review-list', 'review_list')->name('review.list');
    });
    Route::controller(\Modules\Ecommerce\Http\Controllers\Admin\ProductFileController::class)->name('product.files.')->prefix('product-files')->group(function () {
        Route::get('{productId}', 'index')->name('index');
        Route::post('upload/{productId}', 'upload')->name('upload');
        Route::get('set-current/{fileId}', 'setCurrent')->name('set-current');
        Route::get('delete/{fileId}', 'destroy')->name('delete');
    });
    // Order Controller
    Route::controller(OrderController::class)->name('order.')->prefix('order')->group(function (){
        Route::get('/', 'index')->name('index');
        Route::get('view/{id}', 'show')->name('show');
        Route::patch('update-status/{id}','updateStatus')->name('updateStatus');
        Route::patch('payment-status/{id}','paymentStatus')->name('paymentStatus');

    });
    // Shipping Method Controller
    Route::controller(ShippingMethodController::class)->prefix('shipping-method')->name('shipping-method.')->group(function (){
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store/{id?}', 'store')->name('store');
        Route::get('edit/{id?}', 'edit')->name('edit');
        Route::put('status/{id}', 'status')->name('status');
        Route::delete('delete/{id}','delete')->name('delete');
    });
});

// Public FrontEnd

Route::group(['middleware' => ['HtmlSpecialchars', 'MaintenanceMode']], function () {
    // Product Controller
    Route::controller(PublicProductController::class)->name('product.')->group(function (){
        Route::get('/shop', 'shop')->name('shop');
        Route::get('/search', 'search')->name('search');
        Route::get('/product/{slug}', 'product')->name('view');
    });

    // Cart Controller
    Route::controller(CartController::class)->prefix('cart')->name('cart.')->group(function () {
        Route::get('/view', 'cart')->name('cart');
        Route::post('add-to-cart', 'addToCart')->name('add');
        Route::delete('/cart/{id}','destroy')->name('delete');
        Route::post('update', 'update')->name('update');
        Route::get('get-cart-total', 'getCartTotal')->name('getCartTotal');
    });

    // Checkout Controller
    Route::controller(CheckoutController::class)->prefix('checkout')->name('checkout.')->group(function (){
        Route::get('/view', 'index')->name('index');
        Route::get('/process-to-payment', 'processToPayment')->name('process-to-payment');
    });

    // Ecommerce Payment Controller
    Route::controller(EcommercePaymentController::class)->name('ecommerce.')->group(function () {
        Route::post('/pay-stripe', 'stripe')->name('stripe');
        Route::post('/pay-bank', 'bank')->name('bank');
        Route::post('/pay-razorpay', 'pay_via_razorpay')->name('pay-razorpay');
        Route::get('/mollie', 'pay_via_mollie')->name('pay-via-mollie');
        Route::get('/mollie-success', 'mollie_payment_success')->name('mollie-payment-success');
        Route::post('/flutterwave', 'flutterwave')->name('pay-via-flutterwave');
        Route::get('/ecommerce/paystack', 'pay_via_payStack')->name('pay-via-paystack');
        Route::get('/pay-via-instamojo', 'pay_via_instamojo')->name('pay-via-instamojo');
        Route::get('/ecommerce/response-instamojo', 'instamojo_response')->name('response-instamojo');
    });


    Route::controller(UserPaypalController::class)->name('user.')->group(function (){
        Route::get('/pay-via-paypal','paypal')->name('pay-via-paypal');
        Route::get('/paypal-success-payment','paypal_success_payment')->name('paypal-success-payment');
        Route::get('/paypal-failed-payment','paypalFailedPayment')->name('paypalFailedPayment');
    });

    // User Order Code
    Route::controller(UserOrderController::class)->name('user-order.')->prefix('user/order')->group(function (){
        Route::get('all/transactions/history', 'myTransactions')->name('myTransactions');
        Route::post('review-submit', 'reviewSubmit')->name('reviewSubmit');
        Route::get('my/reviews', 'reviews')->name('reviews');
    });

    // Digital Marketplace Routes
    Route::controller(\Modules\Ecommerce\Http\Controllers\Digital\DownloadController::class)->name('user.')->prefix('user/downloads')->group(function () {
        Route::get('/', 'index')->name('downloads');
        Route::get('download/{orderDetailId}', 'download')->name('downloads.file');
    });

    Route::get('/download/token/{token}', [\Modules\Ecommerce\Http\Controllers\Digital\DownloadController::class, 'downloadByToken'])->name('user.downloads.token');

    Route::controller(\Modules\Ecommerce\Http\Controllers\Digital\LicenseController::class)->name('license.')->prefix('api/license')->group(function () {
        Route::post('validate', 'validateLicense')->name('validate');
        Route::post('activate', 'activate')->name('activate');
        Route::post('deactivate', 'deactivate')->name('deactivate');
    });

    Route::get('/user/license/{license}', [\Modules\Ecommerce\Http\Controllers\Digital\LicenseController::class, 'show'])->name('user.license.show');

});

