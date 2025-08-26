<?php

namespace Thebrightlabs\IraqPayments;

use Illuminate\Support\Str;
use Thebrightlabs\IraqPayments\Models\Subscription;

trait withQiCardHelpers
{

    public function preparePayload($data)
    {
        $payload = [
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?: 'IQD',
            'locale' => $data['locale'] ?: "US",
            'description' => $data['description'] ?: "No Description.",
            'customerInfo' => $data["customerInfo"] ?: [],
            'finishPaymentUrl' => route(config('qi_card.finishPaymentUrl')),
            'notificationUrl' => route('payment.webhook'),
            'requestId' => $data["request_id"] ?: (string)Str::uuid(),
            'additionalInfo' => $data["additionalInfo"] ?: [],
        ];
        return $payload;
    }

    public function proccedMonthlyLogicToSubscription(Subscription $proccededSubscription, array $result)
    {
        // then make the subscriotopn date updated from today to next month..
        $today = Carbon::now();
        $nextMonth = $today->copy()->addMonth();
        // lets update the susbcription to be base don these dates.
        $proccededSubscription->update([
            "status" => "paid",
            "start_date" => $today,
            "end_date" => $nextMonth,
            "gateway_response" => json_encode($result)
        ]);

        return $proccededSubscription;

    }

}
