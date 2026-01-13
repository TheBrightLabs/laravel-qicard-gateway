<?php

namespace Thebrightlabs\QiCard\Models\Traits;

use App\Enums\SubscriptionStatuses;
use App\Enums\Tiers;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Builder;
use Thebrightlabs\QiCard\Models\Plan;

trait WithSubscriptionConfigs
{

    public function hasActiveSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'paid')
            ->where(function ($q) {
                $q->where('end_date', '>', now())
                    ->orWhereNull('end_date');
            })
            ->exists();
    }

    public function activeSubscription()
    {
        return $this->subscriptions()
            ->where('status', SubscriptionStatuses::PAID->value)
            ->where(function (Builder $query) {
                // do and operation
                $query->where('end_date', '>', now())
                    ->orWhereNull('end_date'); // means its paid one time
            })
            ->latest('end_date')
            ->first();
    }


    public function activePlan()
    {
        if ($this->activeSubscription()){
            return $this->activeSubscription()->plan;
        }
        return Plan::where('tier_id',Tiers::DEMO->value)->first();
    }

    public function hasOneTimePaymentPlan()
    {
        return $this->activePlan()?->unit_count == 0;
    }

}
