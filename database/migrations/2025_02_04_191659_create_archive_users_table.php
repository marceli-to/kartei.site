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
    Schema::create('archive_user', function (Blueprint $table) {
      $table->foreignId('archive_id')->constrained()->cascadeOnDelete();
      $table->foreignId('user_id')->constrained()->cascadeOnDelete();
      $table->foreignId('role_id')->nullable()->constrained()->cascadeOnDelete();
      $table->foreignId('added_by')->nullable()->constrained('users');
      $table->timestamp('added_at')->nullable();
      $table->primary(['archive_id', 'user_id']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('archive_users');
  }
};
