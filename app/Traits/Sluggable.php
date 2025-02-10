<?php
namespace App\Traits;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

trait Sluggable
{
  protected static function bootSluggable()
  {
    static::creating(function (Model $model) {
      if (empty($model->slug)) {
        $model->slug = static::generateUniqueSlug($model->{static::getSlugSource()});
      }
    });

    static::updating(function (Model $model) {
      if ($model->isDirty(static::getSlugSource())) {
        $model->slug = static::generateUniqueSlug($model->{static::getSlugSource()}, $model->id);
      }
    });
  }

  protected static function generateUniqueSlug(string $value, ?int $ignoreId = null): string
  {
    $slug = Str::slug($value);
    $originalSlug = $slug;
    $count = 1;

    $query = static::where('slug', $slug);
    if ($ignoreId) {
      $query->where('id', '!=', $ignoreId);
    }

    while ($query->exists()) {
      $slug = $originalSlug . '-' . $count++;
      $query = static::where('slug', $slug);
      if ($ignoreId) {
        $query->where('id', '!=', $ignoreId);
      }
    }

    return $slug;
  }

  protected static function getSlugSource(): string
  {
    return 'title';
  }
}