<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('user_subscriptions', function (Blueprint $table) {
        $table->id();
        $table->uuid('uuid')->unique();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('subscription_plan_id')->constrained();
        $table->enum('payment_interval', ['monthly', 'yearly']);
        $table->enum('payment_method', ['card']);
        $table->timestamp('starts_at');
        $table->timestamp('ends_at')->nullable();
        $table->softDeletes();
        $table->timestamps();
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::dropIfExists('user_subscriptions');
    }
};
