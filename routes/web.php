<?php

use Illuminate\Support\Facades\Route;
use Thebrightlabs\IraqPayments\Http\Controllers\PaymentController;

Route::middleware('web')->group(function () {
    Route::get('/payment/finish', [PaymentController::class])
        ->name('payment.finish');
});
