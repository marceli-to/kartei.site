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
    Schema::create('record_tag', function (Blueprint $table) {
      $table->foreignId('record_id')->constrained()->cascadeOnDelete();
      $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
      $table->timestamps();
      $table->primary(['record_id', 'tag_id']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('record_tag');
  }
};
