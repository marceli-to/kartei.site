<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class User extends Authenticatable implements MustVerifyEmail
{
  use HasApiTokens;
  use Notifiable;
  use SoftDeletes;
  use HasUuid;
  
  protected $fillable = [
    'uuid',
    'firstname',
    'name',
    'email',
    'password',
    'company_id',
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
}
