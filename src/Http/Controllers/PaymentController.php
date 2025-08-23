<?php

namespace Thebrightlabs\IraqPayments\Http\Controllers;


use Illuminate\Http\Request;
use Thebrightlabs\IraqPayments\QiCardGateway;


class PaymentController
{

    public function __invoke(Request $request)
    {
        $paymentId = $request->query('paymentId');
        return app(QiCardGateway::class)
            ->handleFinishedPayment($paymentId, $request);
    }


}
