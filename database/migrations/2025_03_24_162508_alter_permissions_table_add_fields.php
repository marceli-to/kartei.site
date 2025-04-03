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
      Schema::table('permissions', function (Blueprint $table) {
        $table->string('display_name')->nullable()->after('guard_name');
        $table->string('group_key')->nullable()->after('display_name');
        $table->string('group_name')->nullable()->after('group_key');
        $table->foreignId('archive_id')->nullable()->constrained()->after('group_name')->cascadeOnDelete();
        $table->integer('order')->default(0)->after('archive_id');
        $table->boolean('publish')->default(1)->after('order');
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
