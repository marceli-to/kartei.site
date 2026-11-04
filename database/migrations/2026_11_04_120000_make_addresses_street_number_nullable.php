<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// The house number is optional in the form and in validation; an empty one
// arrives as null (ConvertEmptyStringsToNull) and failed the insert
return new class extends Migration
{
  public function up(): void
  {
    Schema::table('addresses', function (Blueprint $table) {
      $table->string('street_number')->nullable()->change();
    });
  }

  public function down(): void
  {
    Schema::table('addresses', function (Blueprint $table) {
      $table->string('street_number')->nullable(false)->change();
    });
  }
};
