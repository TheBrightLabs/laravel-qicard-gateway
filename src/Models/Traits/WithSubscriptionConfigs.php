<?php

namespace Thebrightlabs\QiCard\Models\Traits;

use App\Enums\SubscriptionStatuses;
use App\Enums\Tiers;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Builder;
use Thebrightlabs\QiCard\Models\Plan;

trait WithSubscriptionConfigs
{

    private $cachedActiveSubscription = null;

    public function hasActiveSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'paid')
            ->where(function ($q) {
                $q->where('end_date', '>', now())
                    ->orWhereNull('end_date')
                    ->orWhereRaw('DATE_ADD(end_date, INTERVAL COALESCE(grace_period_days, 3) DAY) >= NOW()');
            })
            ->exists();
    }

    public function activeSubscription()
    {
        if ($this->cachedActiveSubscription == null) {
            $this->cachedActiveSubscription = $this->subscriptions()
                ->where('status', SubscriptionStatuses::PAID->value)
                ->where(function (Builder $query) {
                    // do and operation
                    $query->where('end_date', '>', now())
                        ->orWhereNull('end_date') // means its paid one time
                        ->orWhereRaw('DATE_ADD(end_date, INTERVAL COALESCE(grace_period_days, 3) DAY) >= NOW()');
                    // in grace period

                })
                ->latest('end_date')
                ->first();
        }
        return $this->cachedActiveSubscription;
    }


    public function activePlan()
    {
        if ($this->activeSubscription()) {
            return $this->activeSubscription()->plan;
        }
        return Plan::where('tier_id', Tiers::DEMO->value)->first();
    }

    public function hasOneTimePaymentPlan()
    {
        return $this->activePlan()?->unit_count == 0;
    }

    public function isSubscriptionExpired()
    {
        return !$this->hasActiveSubscription();
    }


}
