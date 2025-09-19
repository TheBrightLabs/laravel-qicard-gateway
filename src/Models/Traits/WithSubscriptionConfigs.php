<?php

namespace Thebrightlabs\QiCard\Models\Traits;

use App\Models\Subscription;

trait WithSubscriptionConfigs
{

    public function hasActiveSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'paid')
            ->where('end_date', '>', now())
            ->exists();
    }

    public function activeSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'paid')
            ->where('end_date', '>', now())
            ->latest('end_date')
            ->first();
    }

    public function activeSubscriptions()
    {
        return $this->subscriptions()
            ->where('status', 'paid')
            ->where('end_date', '>', now())
            ->latest('end_date')
            ->get();
    }

    public function activePlan()
    {
        return optional($this->activeSubscription())->plan;
    }

}
