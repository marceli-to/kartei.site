<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Traits\HasUuid;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Traits\HasPermissions;
use \App\Models\ArchiveUser;

class User extends Authenticatable implements MustVerifyEmail
{
  use HasApiTokens;
  use Notifiable;
  use SoftDeletes;
  use HasUuid;
  use HasRoles;
  use HasPermissions;

  protected $fillable = [
    'firstname',
    'name',
    'email',
    'password',
    'color_scheme',
    'color_theme',
    'company_id',
    'email_verified_at',
  ];

  protected $hidden = [
    'password',
    'remember_token',
  ];

  protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
  ];

  public function company(): BelongsTo
  {
    return $this->belongsTo(Company::class);
  }

  public function companies(): BelongsToMany
  {
    return $this->belongsToMany(Company::class);
  }

  public function archives(): BelongsToMany
  {
    return $this->belongsToMany(Archive::class)->withPivot(['role_id', 'added_by', 'added_at']);
  }

  public function address(): MorphOne
  {
    return $this->morphOne(Address::class, 'addressable')->where('is_billing', false);
  }

  public function billingAddress(): MorphOne
  {
    return $this->morphOne(Address::class, 'addressable')->where('is_billing', true);
  }

  public function subscription(): HasOne
  {
    return $this->hasOne(UserSubscription::class);
  }

  public function relatedUsers(): HasManyThrough
  {
    return $this->hasManyThrough(
      User::class,
      ArchiveUser::class, // You'd need to create this pivot model
      'added_by', // Foreign key on pivot table
      'id', // Foreign key on users table
      'id', // Local key on admin's user record
      'user_id' // Local key on pivot table
    )->with('roles');
  }

  public function favorites(): HasMany
  {
    return $this->hasMany(Favorite::class);
  }

  public function getFavorites()
  {
    return $this->favorites()
      ->join('records', 'favorites.record_id', '=', 'records.id')
      ->whereNull('records.deleted_at')
      ->pluck('records.uuid');
  }

  public function creator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function isSuperAdmin(): bool
  {
    return $this->hasRole('Super Admin');
  }

  /**
   * Account owners (self-registered "Admin" role) or Super Admins.
   * They can't be managed by other archive admins.
   */
  public function isAccountAdmin(): bool
  {
    return $this->hasRole(['Super Admin', 'Admin']);
  }

  public function isMemberOf(Archive $archive): bool
  {
    return $this->archiveMembership($archive) !== null;
  }

  /**
   * Admins are attached to their archives without a pivot role.
   */
  public function isAdminOf(Archive $archive): bool
  {
    $membership = $this->archiveMembership($archive);
    return $membership !== null
      && $membership->role_id === null
      && $this->hasRole('Admin');
  }

  /**
   * Per-archive permissions are stored as "<permission>.<archive id>",
   * e.g. "edit.card.12" (see UserPermission\Store).
   */
  public function hasArchivePermission(Archive $archive, string|array $permissions): bool
  {
    if (!$this->isMemberOf($archive)) {
      return false;
    }

    $names = $this->permissions->pluck('name');

    foreach ((array) $permissions as $permission) {
      if ($names->contains($permission . '.' . $archive->id)) {
        return true;
      }
    }

    return false;
  }

  /**
   * Memberships (archive_user rows) keyed by archive id, loaded once per
   * model instance: policies check them several times per request.
   */
  protected ?\Illuminate\Support\Collection $archiveMembershipCache = null;

  private function archiveMembership(Archive $archive): ?object
  {
    $this->archiveMembershipCache ??= \DB::table('archive_user')
      ->where('user_id', $this->id)
      ->get(['archive_id', 'role_id'])
      ->keyBy('archive_id');

    return $this->archiveMembershipCache->get($archive->id);
  }

  /**
   * Call after changing this user's archive memberships.
   */
  public function flushArchiveMemberships(): static
  {
    $this->archiveMembershipCache = null;
    return $this;
  }

  public function hasActiveSubscription(): bool
  {
    return $this->subscription?->isActive() ?? false;
  }

}
