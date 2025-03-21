<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
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

  public function company(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(Company::class);
  }

  public function companies(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
  {
    return $this->belongsToMany(Company::class);
  }

  public function archives(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
  {
    return $this->belongsToMany(Archive::class);
  }

  public function address(): \Illuminate\Database\Eloquent\Relations\MorphOne
  {
    return $this->morphOne(Address::class, 'addressable')->where('is_billing', false);
  }

  public function billingAddress(): \Illuminate\Database\Eloquent\Relations\MorphOne
  {
    return $this->morphOne(Address::class, 'addressable')->where('is_billing', true);
  }

  public function subscription(): \Illuminate\Database\Eloquent\Relations\HasOne
  {
    return $this->hasOne(UserSubscription::class);
  }

  public function relatedUsers(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
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

  public function hasActiveSubscription(): bool
  {
    return $this->subscription->deleted_at === NULL && ($this->subscription->ends_at === null || $this->subscription->ends_at > now());
  }

}
