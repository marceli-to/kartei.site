<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    // Remove duplicate favorites (racing toggles) before adding the unique index
    $duplicates = DB::table('favorites')
      ->select('user_id', 'record_id', DB::raw('MIN(id) as keep_id'))
      ->groupBy('user_id', 'record_id')
      ->havingRaw('COUNT(*) > 1')
      ->get();

    foreach ($duplicates as $row) {
      DB::table('favorites')
        ->where('user_id', $row->user_id)
        ->where('record_id', $row->record_id)
        ->where('id', '!=', $row->keep_id)
        ->delete();
    }

    Schema::table('favorites', function (Blueprint $table) {
      $table->unique(['user_id', 'record_id']);
    });

    // Display numbers: records of a category ordered by creation
    Schema::table('records', function (Blueprint $table) {
      $table->index(['category_id', 'created_at']);
    });

    // Record fields are upserted by (record_id, uuid)
    Schema::table('record_fields', function (Blueprint $table) {
      $table->index(['record_id', 'uuid']);
    });

    // Image deletion looks media up by file name
    Schema::table('media', function (Blueprint $table) {
      $table->index('resized_name');
    });
  }

  public function down(): void
  {
    Schema::table('media', function (Blueprint $table) {
      $table->dropIndex(['resized_name']);
    });
    Schema::table('record_fields', function (Blueprint $table) {
      $table->dropIndex(['record_id', 'uuid']);
    });
    Schema::table('records', function (Blueprint $table) {
      $table->dropIndex(['category_id', 'created_at']);
    });
    Schema::table('favorites', function (Blueprint $table) {
      // MySQL needs an index for the foreign keys while the unique one is dropped
      $table->index('user_id');
      $table->dropUnique(['user_id', 'record_id']);
    });
  }
};
