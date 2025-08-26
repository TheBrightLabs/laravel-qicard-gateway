<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Thebrightlabs\QiCard\QiCardGateway;

Route::post('qi-card/webhook', function (Request $request) {
    $paymentId = $request->input('paymentId');
    $handleFinishedPayment = app(QiCardGateway::class)
        ->handleFinishedPayment($paymentId, $request);
    if ($handleFinishedPayment) {
        return response("OK", 200);
    } else {
        $message = '
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 60vh;">
        <img src="https://i.ytimg.com/vi/W2fTb7iA_Po/hq720.jpg?sqp=-oaymwEhCK4FEIIDSFryq4qpAxMIARUAAAAAGAElAADIQj0AgKJD&rs=AOn4CLAwh2NoyMaMN2E_f-ze5hcpUd2uIg" alt="Warning" width="550" height="300" style="margin-bottom: 20px;">
        <div style="font-size: 1.5rem;  font-weight: bold;">ayo suspicious detected.</div>
    </div>
    ';
        return response($message, 401)
            ->header('Content-Type', 'text/html');
    }


})->name('payment.webhook');
