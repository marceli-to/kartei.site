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
      $table->string('title');
      $table->string('acronym', 10);
      $table->foreignId('media_id')->nullable()->constrained()->nullOnDelete();
      $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
      $table->timestamps();
      $table->softDeletes();
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