<?php
namespace App\Traits;
use Illuminate\Support\Str;

trait HasUuid
{
  /**
   * Boot the trait, registering the model event listener.
   */
  protected static function bootHasUuid()
  {
    static::creating(function ($model) {
      if (empty($model->uuid)) {
        $model->uuid = (string) Str::uuid();
      }
    });
  }
}