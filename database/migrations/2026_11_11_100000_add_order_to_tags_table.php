<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Tags can be sorted by drag + drop (Stackr #127); existing tags keep their creation order
return new class extends Migration
{
  public function up(): void
  {
    Schema::table('tags', function (Blueprint $table) {
      $table->unsignedInteger('order')->default(0)->after('name');
    });

    DB::table('tags')->orderBy('id')->get(['id', 'archive_id'])->groupBy('archive_id')->each(
      fn ($tags) => $tags->values()->each(
        fn ($tag, $index) => DB::table('tags')->where('id', $tag->id)->update(['order' => $index])
      )
    );
  }

  public function down(): void
  {
    Schema::table('tags', function (Blueprint $table) {
      $table->dropColumn('order');
    });
  }
};
