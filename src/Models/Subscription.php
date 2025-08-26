<?php

namespace Thebrightlabs\QiCard\Models;


use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Subscription extends Model
{
    use HasFactory;

    protected $table = "subscriptions";

    protected $fillable = [
        'user_id',
        'plan_id',
        'amount',
        'currency',
        'gateway',
        'payment_method',
        'invoice_id',
        'invoice_url',
        'payment_id',
        'start_date',
        'end_date',
        'status',
        'gateway_response',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }


    protected function casts(): array
    {
        return [
            'amount' => 'decimal:3',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'gateway_response' => 'array',
        ];
    }
}
