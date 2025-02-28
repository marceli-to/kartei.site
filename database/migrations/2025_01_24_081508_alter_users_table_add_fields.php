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
      Schema::table('users', function (Blueprint $table) {
        $table->uuid('uuid')->unique()->after('id');
        $table->string('firstname')->after('uuid');
        $table->enum('color_scheme', ['dark', 'light'])->default('light')->after('remember_token');
        $table->enum('color_theme', ['ice', 'candy', 'lime', 'lemon'])->default('ice')->after('color_scheme');
        $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete()->after('color_theme');
        $table->softDeletes()->after('company_id');
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
