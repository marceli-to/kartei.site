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
    Schema::create('archive_templates', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->boolean('image')->default(true);
      $table->foreignId('archive_id')->constrained()->onDelete('cascade');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('archive_templates');
  }
};
