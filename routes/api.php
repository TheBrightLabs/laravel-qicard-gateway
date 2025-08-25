<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Thebrightlabs\IraqPayments\QiCardGateway;

Route::post('qi-card/webhook', function (Request $request) {
    $paymentId = $request->input('paymentId');
    $handleFinishedPayment = app(QiCardGateway::class)
        ->handleFinishedPayment($paymentId, $request);
    return response('OK', 200);
})->name('payment.webhook');
