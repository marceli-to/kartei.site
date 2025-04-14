<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUuid;

class ArchiveTemplate extends Model
{
  use HasUuid;

  protected $fillable = [
    'image',
    'archive_id',
  ];

  public function fields(): HasMany
  {
    return $this->hasMany(TemplateField::class)->orderBy('order');
  }

  public function archive(): BelongsTo
  {
    return $this->belongsTo(Archive::class);
  }
}
