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
    Schema::create('record_categories', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->string('number');
      $table->string('acronym', 10);
      $table->foreignId('record_id')->constrained()->cascadeOnDelete();
      $table->foreignId('numeral_id')->constrained()->cascadeOnDelete();
      $table->foreignId('parent_id')->nullable()->constrained('record_categories')->nullOnDelete();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('record_categories');
  }
};