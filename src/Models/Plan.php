<?php

namespace Thebrightlabs\IraqPayments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    protected function casts(): array
    {
        return [
            'price' => 'decimal:3',
            'is_active' => 'boolean',
            "features" => "array"
        ];
    }
}
