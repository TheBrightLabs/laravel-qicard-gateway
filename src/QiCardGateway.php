<?php

namespace Thebrightlabs\IraqPayments;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Thebrightlabs\IraqPayments\Models\Plan;
use Thebrightlabs\IraqPayments\Models\Subscription;
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
        // prepare payload
        // make payment
        // make susbcription for the created payment
        $user = auth()->user();
        $plan = Plan::findOrFail($data['plan_id'])->first();

        $payload = $this->preparePayload($data);
        $createdPayment = $this->makePayment($payload);
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'currency' => $data['currency'] ? "IQD" : '',
            'gateway' => 'QiCard',
            'payment_method' => 'QiCard',
            'transaction_id' => $result['paymentId'] ?? null,
            'invoice_url' => $result['formUrl'] ?? null,
            'status' => 'pending',
            'gateway_response' => json_encode($result),
        ]);

        return $subscription;

    }

    public function preparePayload($data)
    {
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

        return $data;
    }

    public function makePayment($payload)
    {
        $apiHost = $this->getApiHost();
        $username = $this->getUsername();
        $password = $this->getPassword();
        $terminalId = $this->getTerminalId();

        $response = Http::withBasicAuth($username, $password)
            ->withHeaders([
                'X-Terminal-Id' => $terminalId,
                'Accept' => 'application/json',
            ])
            ->post($apiHost . '/payment', $payload);

        return $response->json();

    }

}
