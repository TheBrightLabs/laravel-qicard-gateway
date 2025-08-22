<?php

namespace Thebrightlabs\IraqPayments;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Thebrightlabs\IraqPayments\Traits\withQiCardConfigs;

class QiCardGateway
{
    // Bismillah.
    use withQiCardConfigs;

    public function checkStatus(string $paymentId)
    {
        // very basic status check
        $apiHost = $this->getApiHost();
        $url = $this->getApiHost() . "/payment/{$paymentId}/status";
        $username = $this->getUsername();
        $password = $this->getPassword();

        $response = Http::withBasicAuth($username, $password)
            ->withHeaders([
                'X-Terminal-Id' => $this->getTerminalId(),
                'Accept' => 'application/json',
            ])
            ->get($url);

        return $response->json();
    }

    public function makeSubscription(array $data)
    {
        $apiHost = $this->getApiHost();
        $username = $this->getUsername();
        $password = $this->getPassword();
        $terminalId = $this->getTerminalId();

        $payload = [
            'amount' => $data['amount'],
            'currency' => $data['currency'] ? "IQD" : '',
            'description' => $data['description'],
            'customer' => [
                'name' => $data['customer_name'],
                'email' => $data['customer_email'],
            ],
            'requestId' => (string)Str::uuid(),
            'callbackUrl' => $data['callbackUrl'], // or your own callback URL
            'additionalInfo' => [
                'plan_id' => $data['plan_id'],
                'user_id' => $data['user_id'],
            ],
        ];

        $response = Http::withBasicAuth($username, $password)
            ->withHeaders([
                'X-Terminal-Id' => $terminalId,
                'Accept' => 'application/json',
            ])
            ->post($apiHost . '/payment', $payload);

        return $response->json();

    }
}
