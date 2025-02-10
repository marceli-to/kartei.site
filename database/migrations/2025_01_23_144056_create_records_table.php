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
    Schema::create('records', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->string('slug', 255)->unique();
      $table->string('title');
      $table->string('acronym', 10);
      $table->json('attributes');
      $table->foreignId('archive_id')->constrained()->cascadeOnDelete();
      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('records');
  }
};
