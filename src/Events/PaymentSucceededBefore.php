<?php

namespace Thebrightlabs\QiCard\Events;


use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Thebrightlabs\QiCard\Models\Subscription;

class PaymentSucceededBefore
{
use Dispatchable,SerializesModels;

    public $result;
    public $subscription;
    public $request;

    public function __construct(array $result, Subscription $subscription, $request = null)
    {
        $this->result = $result;
        $this->subscription = $subscription;
        $this->request = $request;
    }
}
