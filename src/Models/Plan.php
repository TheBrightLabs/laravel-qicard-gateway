<?php

namespace Thebrightlabs\QiCard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Thebrightlabs\QiCard\withQiCardHelpers;

class Plan extends Model
{
    use HasFactory, withQiCardHelpers;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'price',
        'description',
        'features',
        'is_active',
        'order',
        'unit_count'
    ];

    public function getSubscriptionCountAttribute()
    {
        return $this->subscriptions()->count();
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function isMonthly()
    {
        return $this->type == "monthly";
    }

    /**
     * Check if the plan is a lifetime plan (no expiration).
     *
     * A plan is considered lifetime if:
     * 1. It's a one-time payment plan (type = "one_time")
     * 2. OR it has zero duration (unit_count = 0) and requires payment (price > 0)
     *
     * Note: Free plans (price = 0) are NOT considered lifetime since they
     * are handled separately and don't go through payment flow.
     *
     * @return bool
     */
    public function isLifeTime()
    {
        return  $this->type == "one_time" || ($this->unit_count == 0 && (int)$this->price > 0);
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:3',
            'is_active' => 'boolean',
            "features" => "array"
        ];
    }

}
