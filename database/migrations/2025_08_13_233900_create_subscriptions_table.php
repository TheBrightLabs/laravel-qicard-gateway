<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('plan_id')
                ->constrained('plans');
            $table->decimal('amount', 12, 3);
            $table->string('currency')->default('IQD');
            $table->string('gateway')->default('QiCard');
            $table->string('payment_method')->default('QICard');

            $table->string('invoice_id')->nullable();
            $table->string('invoice_url')->nullable();
            $table->string('payment_id')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('status')->default('pending');
            $table->json('gateway_response')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'plan_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
