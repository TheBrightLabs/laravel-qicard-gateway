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

    public function isLifeTime()
    {
        return $this->type == "free" || $this->type == "one_time" || $this->unit_count == 0;
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
