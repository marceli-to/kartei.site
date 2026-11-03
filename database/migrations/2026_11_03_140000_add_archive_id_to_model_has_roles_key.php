<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * The primary key (role_id, model_id, model_type) allowed a role only once per
 * user, so the same role could not be held for two archives. archive_id is
 * nullable (account-wide roles such as Admin), so it cannot be part of a
 * primary key; a unique index takes its place.
 */
return new class extends Migration
{
  public function up(): void
  {
    // Add the new index first: the role_id foreign key needs an index once the primary key is gone
    Schema::table('model_has_roles', function (Blueprint $table) {
      $table->unique(['role_id', 'model_id', 'model_type', 'archive_id'], 'model_has_roles_role_model_archive_unique');
    });

    Schema::table('model_has_roles', function (Blueprint $table) {
      $table->dropPrimary('model_has_roles_role_model_type_primary');
    });
  }

  public function down(): void
  {
    Schema::table('model_has_roles', function (Blueprint $table) {
      $table->primary(['role_id', 'model_id', 'model_type'], 'model_has_roles_role_model_type_primary');
    });

    Schema::table('model_has_roles', function (Blueprint $table) {
      $table->dropUnique('model_has_roles_role_model_archive_unique');
    });
  }
};
