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
    Schema::create('archive_structure', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->string('number', 10);
      $table->string('name');
      $table->string('custom_id', 25);
      $table->enum('numeral_type', ['decimal', 'alpha', 'roman'])->default('decimal');
      $table->enum('custom_id_type', ['auto', 'manual'])->default('auto');
      $table->unsignedInteger('order')->default(0);
      $table->foreignId('archive_id')->constrained()->onDelete('cascade');
      $table->foreignId('parent_id')->nullable()->constrained('archive_structure')->onDelete('cascade');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('archive_structures');
  }
};
