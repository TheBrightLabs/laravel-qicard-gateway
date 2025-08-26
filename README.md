
![Logo](https://i.postimg.cc/HWFHCrWm/qicard.jpg)

# Laravel QiCard Gateway

A simple, secure Laravel package for integrating the Qi Card payment gateway in your application.  
This package handles all payment, subscription, and webhook logic for you—just plug it into your project and start accepting Qi Card payments in Iraq.

---

## Features

- Easy Qi Card payment integration for Laravel
- Ready-to-use plans and subscriptions tables and migrations
- Secure payment status checks (never trust webhook status alone)
- Webhook support with built-in replay and protection (different verification than the one QiCard proivdes)
- All configuration through environment variables—no code changes needed
- Includes a default seeder for demo plans

---

## Installation

```bash
composer require thebrightlabs/laravel-qicard-gateway
```

---

## Setup

1. **Publish config and migrations:**

   ```bash
   php artisan vendor:publish --provider="Thebrightlabs\QiCard\QiCardServiceProvider"
   ```

   This publishes the config file and database migrations for `plans` and `subscriptions`.

2. **Run migrations:**

   ```bash
   php artisan migrate
   ```

3. **(Optional) Seed demo plans:**

   ```bash
   php artisan db:seed --class="Thebrightlabs\QiCard\Seeders\PlansTableSeeder"
   ```

   You can edit, add, or remove plans later using CRUD.

---

## Configuration

- All settings are in your `.env` file.  
- **Sandbox mode** (for testing) is enabled by default; production keys are only needed when you go live.

**Example `.env`:**

```
QI_CARD_MODE=sandbox

# For production, set these:
QI_CARD_API_HOST=your_production_api_url
QI_CARD_USERNAME=your_production_username
QI_CARD_PASSWORD=your_production_password
QI_CARD_TERMINAL_ID=your_production_terminal_id

QI_FINISH_PAYMENT_URL=https://your-app.com/payment/finish
QI_NOTIFICATION_URL=https://your-app.com/qi-card/webhook
```

---

## Usage

### 1. Show Plans and Handle Subscriptions

Display your plans using the `plans` table.  
When a user clicks "Subscribe," send a GET request to your payment route with the plan ID.

**Example Blade/Volt:**
```blade
@foreach($plans as $plan)
    <div>
        <h3>{{ $plan->name }}</h3>
        <p>{{ $plan->description }}</p>
        @if($plan->price > 0)
            <a href="{{ route('submit', $plan->id) }}">Subscribe</a>
        @endif
    </div>
@endforeach
```

**Example Route:**
```php
Route::get('/payment/plan/{id}', function ($planId) {
    $plan = Plan::find($planId);
    $user = auth()->user();
    $qiCardGateway = app(QiCardGateway::class);
    $data = [
        'amount' => $plan->price,
        'locale' => app()->getLocale(),
        'description' => $plan->description,
        'currency' => 'IQD',
        'customerInfo' => [
            'customer_name' => $user->name,
            'customer_email' => $user->email,
        ],
        'request_id' => (string)Str::uuid(),
        'additionalInfo' => [
            "user_id" => $user->id,
            "planType" => $plan->type,
            "planUnitCount" => $plan->unit_count,
        ],
    ];
    return $qiCardGateway->makeSubscription($data, $plan);
})->name('submit');
```

### 2. Payment Flow

- User is redirected to Qi Card to complete payment.
- After payment, Qi Card redirects back to your `finishPaymentUrl` and/or calls your `notificationUrl` (webhook).

---

## Webhook & Status Checks

- **Never trust the webhook status directly.**
- When a webhook is received, the package checks the payment status with Qi Card’s official API before updating your subscription.
- Only if the status matches, the subscription is marked as `paid` or `cancelled`.
- If the webhook is suspicious or doesn’t match, it is ignored.

![Logo](https://i.postimg.cc/mgCgnQ7K/Screenshot-2025-08-25-at-5-54-29-PM.png)

---

## Automatic Pending Subscription Cleanup

- Some users may never finish payment.  
- The package provides a console command to check all pending subscriptions and update their status automatically.

**Add to your scheduler (in `routes/console.php`):**
```php
Schedule::exec('qiCard:status-check')
    ->everyTenMinutes();
```
- This command checks all pending subscriptions and updates them to `cancelled` if not completed.

---

## Plan & Subscription Statuses

- **pending**: User started payment but hasn’t finished yet.
- **paid**: Payment succeeded and subscription is active.
- **cancelled**: Payment was cancelled or failed.

---

## Customization

- All config is via `.env`—no code changes needed for production or sandbox.
- Default URL for webhook is recommended for best security and compatibility.

---

## Security

- Webhook requests are always verified against Qi Card’s API, not trusted directly.
- You can safely use this package in production.

---

## License

MIT

---

## Credits

Developed by TheBrightLabs  
[https://thebrightlabs.co](https://thebrightlabs.co)

---

Let us know if you have questions or suggestions!
