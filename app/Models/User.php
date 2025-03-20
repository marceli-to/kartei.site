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

  public function hasActiveSubscription(): bool
  {
    return $this->subscription->deleted_at === NULL && ($this->subscription->ends_at === null || $this->subscription->ends_at > now());
  }

}
