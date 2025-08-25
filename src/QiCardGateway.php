<?php

namespace Thebrightlabs\IraqPayments;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Thebrightlabs\IraqPayments\Models\Plan;
use Thebrightlabs\IraqPayments\Models\Subscription;

class QiCardGateway
{
    // Bismillah.
    use withQicardHelpers, withQiCardConfigs;

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
            'payment_method' => 'CARD',
            'payment_id' => $createdPayment['paymentId'],
            'invoice_id' => $createdPayment['requestId'],
            'invoice_url' => $createdPayment['formUrl'] ?? null,
            'status' => "pending",
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
            'notificationUrl' => $data["notificationUrl"] ?: route('payment.webhook'),
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

    public function handleFinishedPayment(string $payemntId, $request = null)
    {
        $subscription = Subscription::where('payment_id', $payemntId)->first();
        $result = $this->getPaymentResult($payemntId); // get the status
        // check if its a request (not shcedulers will be checked)
        if ($request) {
            // check if its from qi card
            if ($request->input('status') != $result['status']) {
                // if not from qi card return null
                return null;
            }
        }

        // handle if payment succeed
        if (isset($result['status'])) {
            // if new status is success and not canceled
            if ($result['status'] == "SUCCESS" && !$result["canceled"]) {
                return $this->handleSucceededPayment($result, $request);
            } else {
                // if not success, means its failed or still in pending mark it as failed..
                return $this->handleFailedPayment($result, $request);
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

    public function handleSucceededPayment(array $result, $request = null)
    {
        $proccededSubscription = Subscription::where('payment_id', $result['paymentId'])->first();
        $choosenPlan = $proccededSubscription->plan;
        if ($choosenPlan->isMonthly()) {
            // then make the subscriotopn date updated from today to next month..
            $today = Carbon::now();
            $nextMonth = $today->copy()->addMonth();
            // lets update the susbcription to be base don these dates.
            $proccededSubscription->update([
                "status" => "paid",
                "start_date" => $today,
                "end_date" => $nextMonth,
                "gateway_response" => $result
            ]);

            return redirect()->route("client.payment")->with("message", "Payment succeeded, your subscription is now active.")->with("type", "success");

        } else {
            if ($choosenPlan->isLifeTime()) {
                $today = Carbon::now();
                $proccededSubscription->update([
                    "status" => "paid",
                    "start_date" => $today,
                    "gateway_response" => $result,
                ]);

                return redirect()->route("client.payment")->with("message", "Payment succeeded, your subscription is now active.")->with("type", "success");

            } else {
                // means its not lifetime, we should keep the end date
                $today = Carbon::now();
                $daysToAdd = intval($choosenPlan->unit_count);
                $dateToExpire = $today->copy()->addDays($daysToAdd);
                $proccededSubscription->update([
                    "status" => "paid",
                    "start_date" => $today,
                    "end_date" => $dateToExpire,
                    "gateway_response" => $result
                ]);

                return redirect()->route("client.payment")->with("message", "Payment succeeded, your subscription is now active.")->with("type", "success");

            }
        }


    }

    public function handleFailedPayment(array $result, $request = null)
    {
        $proccededSubscription = Subscription::where('payment_id', $result['paymentId'])->first();
        $proccededSubscription->update([
            "status" => "cancelled",
            "checked" => true,
            "gateway_response" => $result
        ]);
    }
}
