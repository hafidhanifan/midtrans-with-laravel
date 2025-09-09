<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/checkout', [PaymentController::class, 'checkoutPage']);
Route::post('/payment/create', [PaymentController::class, 'createTransaction']);


// endpoint notification yang dipanggil Midtrans
Route::post('/midtrans/notification', [PaymentController::class, 'notification']);