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
    Schema::create('archives', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->string('slug', 255)->unique();
      $table->string('name');
      $table->string('acronym', 10);
      $table->json('settings')->nullable();
      $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('archives');
  }
};
