<?php

namespace Thebrightlabs\IraqPayments\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Thebrightlabs\IraqPayments\QiCardGateway;


class PaymentController extends Controller
{

    public function __invoke(Request $request)
    {
        $paymentId = $request->query('paymentId');
        return app(QiCardGateway::class)
            ->handleFinishedPayment($paymentId, $request);

    }

}
