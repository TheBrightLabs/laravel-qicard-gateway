<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->text("description");
            $table->string('name');
            $table->decimal("unit_count", 6, 3);
            // unit count is using days to calculate end dates, when unit type is daily unit count is 1,
            // means the end date will be 1 day after today, and same for any other which u must add the unit counts carefully
            // for monthly keep it as 30, in the service we already check if the type is
            // monthly and unit count is 30, means its monthly and we procced monthly based not only 30
            $table->string('slug')->unique();
            $table->string('type');
            $table->decimal('price', 12, 3);
            $table->json('features');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
