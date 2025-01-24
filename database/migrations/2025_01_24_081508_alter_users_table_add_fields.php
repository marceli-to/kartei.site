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
          $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete()->after('remember_token');
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
