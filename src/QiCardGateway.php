<?php

namespace Thebrightlabs\QiCard;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Thebrightlabs\QiCard\Events\PaymentSucceededAfter;
use Thebrightlabs\QiCard\Events\PaymentSucceededBefore;
use Thebrightlabs\QiCard\Models\Plan;
use Thebrightlabs\QiCard\Models\Subscription;

class QiCardGateway
{
    // Bismillah.
    use withQiCardHelpers, withQiCardConfigs;

    public function makeSubscription(array $data, Plan $plan)
    {
        // prepare payload
        // make payment
        // make susbcription for the created payment
        $user = auth()->user();
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

        return redirect()->to($subscription->invoice_url);
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

        // Check if result has status
        if (!isset($result['status'])) {
            return redirect()->route($this->getFinishPaymentUrl())
                ->with('message', 'Payment not found, please try again or contact support.')
                ->with('type', 'error');
        }


        // check if its a request (not shcedulers will be checked)
        if ($request) {
            // check if its from qi card
            if ($request->input('status') != $result['status']) {
                // if not from qi card return null
                return null;
            }
        }

              // handle if payment succeed
            // if new status is success and not canceled
            if ($result['status'] == "SUCCESS" && !$result["canceled"]) {
                return $this->handleSucceededPayment($result, $request);
            } else {
                // if not success, means its failed or still in pending mark it as failed..
                return $this->handleFailedPayment($result, $request);
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
        Event::dispatch(new PaymentSucceededBefore($result, $proccededSubscription, $request));

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
                "gateway_response" => json_encode($result)
            ]);


        } else {
            if ($choosenPlan->isLifeTime()) {
                $today = Carbon::now();
                $proccededSubscription->update([
                    "status" => "paid",
                    "start_date" => $today,
                    "gateway_response" => json_encode($result),
                ]);

            } else {
                // means its not lifetime, we should keep the end date
                $today = Carbon::now();
                $daysToAdd = intval($choosenPlan->unit_count);
                $dateToExpire = $today->copy()->addDays($daysToAdd);
                $proccededSubscription->update([
                    "status" => "paid",
                    "start_date" => $today,
                    "end_date" => $dateToExpire,
                    "gateway_response" => json_encode($result)
                ]);


            }
        }

        // AFTER EVENT - for additional logic after package processing
        Event::dispatch(new PaymentSucceededAfter($result, $proccededSubscription, $request));


        return redirect()->route($this->getFinishPaymentUrl())->with("message", "Payment succeeded, your subscription is now active.")->with("type", "success");
    }

    public function handleFailedPayment(array $result, $request = null)
    {
        $proccededSubscription = Subscription::where('payment_id', $result['paymentId'])->first();
        $proccededSubscription->update([
            "status" => "cancelled",
            "gateway_response" => json_encode($result),
        ]);

        if ($request) {
            return redirect()->route($this->getFinishPaymentUrl())
                ->with("message", "Payment not Procceded, please try again or contact support.")
                ->with("type", "error");

        }

    }
}
