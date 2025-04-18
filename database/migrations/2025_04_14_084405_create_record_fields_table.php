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
      Schema::create('record_fields', function (Blueprint $table) {
        $table->id();
        $table->string('uuid');
        $table->string('placeholder');
        $table->string('content');
        $table->foreignId('record_id')->constrained()->cascadeOnDelete();
        $table->timestamps();
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::dropIfExists('record_fields');
    }
};
