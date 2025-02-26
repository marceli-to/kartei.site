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
    Schema::create('addresses', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->string('company')->nullable();
      $table->string('byline')->nullable();
      $table->string('street');
      $table->string('street_number');
      $table->string('zip');
      $table->string('city');
      $table->string('country');
      $table->boolean('is_billing')->default(false);
      $table->morphs('addressable');
      $table->softDeletes();      
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('addresses');
  }
};