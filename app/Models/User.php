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
    'uuid',
    'firstname',
    'name',
    'email',
    'password',
    'color_scheme',
    'color_theme',
    'company_id',
    'email_verified_at',
    'deleted_at',
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
      ->pluck('records.uuid');
  }

  public function hasActiveSubscription(): bool
  {
    return $this->subscription->deleted_at === NULL && ($this->subscription->ends_at === null || $this->subscription->ends_at > now());
  }

}
