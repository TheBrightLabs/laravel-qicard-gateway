<?php

use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('payment/finish', function () {
        return redirect()->route("client.payment");
    })->name('payment.finish');
});
