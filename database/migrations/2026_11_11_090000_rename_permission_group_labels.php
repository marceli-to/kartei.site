<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/*
 * Labels in the user permissions form (Stackr #136): the two "Bearbeiten" groups become
 * "Kartei / Eigenschaften" and "Karten / Eigenschaften", "Exportieren" becomes "Export".
 * By group key and name prefix, so the per-archive copies (<name>.<archive id>) change too.
 */
return new class extends Migration
{
  public function up(): void
  {
    $this->rename('Kartei / Eigenschaften', 'Karten / Eigenschaften', 'Export');
  }

  public function down(): void
  {
    $this->rename('Bearbeiten', 'Bearbeiten', 'Exportieren');
  }

  private function rename(string $archiveEdit, string $cardEdit, string $export): void
  {
    DB::table('permissions')->where('group_key', 'archive_edit')->update(['group_name' => $archiveEdit]);
    DB::table('permissions')->where('group_key', 'card_edit')->update(['group_name' => $cardEdit]);
    DB::table('permissions')
      ->where(fn ($query) => $query->where('name', 'export.card')->orWhere('name', 'like', 'export.card.%'))
      ->update(['display_name' => $export]);
  }
};
