<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/*
 * addresses.country and companies.country: German name -> ISO code ("Schweiz" -> "CH").
 * Values that are not in the list stay as they are and are logged; the form then
 * asks for a valid country on the next save.
 */
return new class extends Migration
{
  private const TABLES = ['addresses', 'companies'];

  public function up(): void
  {
    $this->convert(array_flip($this->countries()));
  }

  public function down(): void
  {
    $this->convert($this->countries());
  }

  // The current list (code => name); up() needs the names as they were stored, so run it before renaming any
  private function countries(): array
  {
    return json_decode(file_get_contents(resource_path('data/countries.json')), true);
  }

  private function convert(array $map): void
  {
    foreach (self::TABLES as $table) {
      foreach ($map as $from => $to) {
        DB::table($table)->where('country', $from)->update(['country' => $to]);
      }

      $unknown = DB::table($table)
        ->whereNotNull('country')
        ->whereNotIn('country', array_values($map))
        ->distinct()
        ->pluck('country');

      if ($unknown->isNotEmpty()) {
        Log::warning("Countries not converted in {$table}", $unknown->all());
      }
    }
  }
};
