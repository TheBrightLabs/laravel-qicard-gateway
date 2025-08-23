<?php

namespace Thebrightlabs\IraqPayments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Thebrightlabs\IraqPayments\withQiCardHelpers;

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

    protected function casts(): array
    {
        return [
            'price' => 'decimal:3',
            'is_active' => 'boolean',
            "features" => "array"
        ];
    }

}
