<?php

namespace Thebrightlabs\IraqPayments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Thebrightlabs\IraqPayments\Models\Subscription;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'price',
        'description',
        'features',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:3',
            'is_active' => 'boolean',
            "features"=>"array"
        ];
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function getSubscriptionCountAttribute()
    {
        return $this->subscriptions()->count();
    }
}
