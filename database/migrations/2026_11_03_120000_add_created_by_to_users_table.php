<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Track which admin created a user account, so only that admin
   * may edit the account's profile (name, email) or delete it.
   */
  public function up(): void
  {
    Schema::table('users', function (Blueprint $table) {
      $table->foreignId('created_by')->nullable()->after('company_id')->constrained('users')->nullOnDelete();
    });

    // Backfill: the admin who first added a user to an archive is treated as its creator
    $creators = DB::table('archive_user')
      ->whereNotNull('added_by')
      ->whereColumn('added_by', '!=', 'user_id')
      ->orderBy('added_at')
      ->get(['user_id', 'added_by'])
      ->unique('user_id');

    foreach ($creators as $row) {
      DB::table('users')
        ->where('id', $row->user_id)
        ->whereNull('created_by')
        ->update(['created_by' => $row->added_by]);
    }
  }

  public function down(): void
  {
    Schema::table('users', function (Blueprint $table) {
      $table->dropConstrainedForeignId('created_by');
    });
  }
};
