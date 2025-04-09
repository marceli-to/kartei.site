<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUuid;

class TemplateField extends Model
{
  use HasUuid;

  protected $fillable = [
    'placeholder',
    'archive_template_id',
  ];

  public function template(): BelongsTo
  {
    return $this->belongsTo(ArchiveTemplate::class);
  }
}
