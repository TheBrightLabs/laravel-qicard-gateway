<?php

namespace Thebrightlabs\IraqPayments;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Thebrightlabs\IraqPayments\Models\Plan;
use Thebrightlabs\IraqPayments\Models\Subscription;
use Thebrightlabs\IraqPayments\Traits\withQiCardConfigs;

class QiCardGateway
{
    // Bismillah.
    use withQiCardConfigs;

    public function makeSubscription(array $data)
    {
        // prepare payload
        // make payment
        // make susbcription for the created payment
        $user = auth()->user();
        $plan = Plan::find($data['plan_id']);
        $payload = $this->preparePayload($data);
        $createdPayment = $this->makePayment($payload);
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'currency' => $data['currency'] ?: "IQD",
            'gateway' => 'QiCard',
            'payment_method' => 'QiCard',
            'payment_id' => $createdPayment['paymentId'],
            'invoice_id' => $createdPayment['requestId'],
            'invoice_url' => $createdPayment['formUrl'] ?? null,
            'status' => $createdPayment['status'],
            'gateway_response' => json_encode($createdPayment),
        ]);

        if ($subscription) {
            return redirect()->to($subscription->invoice_url);
        } else {
            throw new Exception('Failed to create subscription');
        }

    }

    public function preparePayload($data)
    {
        $payload = [
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?: 'IQD',
            'locale' => $data['locale'] ?: "US",
            'description' => $data['description'] ?: "No Description.",
            'customerInfo' => $data["customerInfo"] ?: [],
            'finishPaymentUrl' => $data["finishPaymentUrl"] ?: route('payment.finish'),
            'notificationUrl' => $data["notificationUrl"] ?: route('payment.finish'),
            'requestId' => $data["request_id"] ?: (string)Str::uuid(),
            'additionalInfo' => $data["additionalInfo"] ?: [],
        ];

        return $payload;
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

    public function handleFinishedPayment(string $payemntId, Request $request)
    {
        $subscription = Subscription::where('payment_id', $payemntId)->first();
        $result = $this->getPaymentResult($payemntId); // get the status
        // handle if payment succeed
        if (isset($result['status'])) {
            if ($result['status'] == "SUCCESS") {
                return $this->handleSucceededPayment($result, $request);
            }
        } else {
            return redirect()->route('client.payment')
                ->with("message", "Payment not found, please try again or contact support.")
                ->with("type", "error");
        }

    }

    public function getPaymentResult(string $paymentId)
    {
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

    public function handleSucceededPayment(array $result, Request $request)
    {

    }
}
