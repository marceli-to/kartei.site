<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class Address extends Model
{
  use HasUuid;
  use SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'uuid',
    'company',
    'byline',
    'street',
    'street_number',
    'zip',
    'city',
    'country',
    'is_billing',
    'addressable_id',
    'addressable_type',
  ];

  /**
   * Get the parent addressable model (user or company).
   */
  public function addressable()
  {
    return $this->morphTo();
  }
}