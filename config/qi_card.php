<?php

return [
    'mode' => env('QI_CARD_MODE', 'sandbox'),
    "finishPaymentUrl" => env('QI_FINISH_PAYMENT_URL', "payment.finish-test"),
    "notificationUrl" => env('QI_NOTIFICATION_URL', "payment.webhook"),
    'sandbox' => [
        'api_host' => 'https://uat-sandbox-3ds-api.qi.iq/api/v1',
        'username' => 'paymentgatewaytest',
        'password' => 'WHaNFE5C3qlChqNbAzH4',
        'terminal_id' => '237984',
    ],

    'production' => [
        'api_host' => env('QI_CARD_API_HOST', ''),
        'username' => env('QI_CARD_USERNAME', ''),
        'password' => env('QI_CARD_PASSWORD', ''),
        'terminal_id' => env('QI_CARD_TERMINAL_ID', ''),
    ],

];
