<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasUuid;

class Record extends Model
{
  use SoftDeletes;
  use HasUuid;

  protected $fillable = [
    'uuid',
    'category_id',
    'archive_id',
  ];

  public function archive(): BelongsTo
  {
    return $this->belongsTo(Archive::class);
  }

  public function tags(): BelongsToMany
  {
    return $this->belongsToMany(Tag::class);
  }

  public function fields(): HasMany
  {
    return $this->hasMany(RecordField::class);
  }
  
  public function media()
  {
    // chaperone: each image knows its record without another query
    return $this->morphMany(Media::class, 'mediable')->chaperone('mediable');
  }

  public function category(): BelongsTo
  {
    return $this->belongsTo(Category::class);
  }

  /**
   * Get the display number for the record.
   *
   * Format: 
   * - For categories:
   *  [category_number].[record_index] / [ARCHIVE_ACRONYM]_[CATEGORY_ACRONYM]
   * - For registers:
   *  [category_number].[record_index] / [ARCHIVE_ACRONYM]_[PARENT_ACRONYM]_[REGISTER_ACRONYM]
   *
   * Example:
   * - 1.0001 / AI_CH
   * - 2.1.0002 / AI_CL_SO (where 2.1 is a register under category 2)
   *
   * @return string
   */
  /**
   * Position (0-based) within the category, when computed in bulk for a list
   * (see Actions\Record\Get). The number is positional on purpose: it shifts
   * when an earlier record in the category is deleted.
   */
  protected ?int $displayIndex = null;

  public function setDisplayIndex(int $index): static
  {
    $this->displayIndex = $index;
    return $this;
  }

  public function getDisplayNumberAttribute(): string
  {
    $category = $this->category;
    $archive = $this->archive;

    // Optional safety checks
    if (!$category || !$archive) {
      return '';
    }

    // Index of this record within its category, ordered by created_at (id breaks ties)
    $index = $this->displayIndex
      ?? $category->records()->orderBy('created_at')->orderBy('id')->pluck('id')->search($this->id);

    if ($index === false) {
      return '';
    }

    return static::formatDisplayNumber($category, $archive, $index);
  }

  /**
   * "<category number>.<position, 4 digits> / <acronym>_[<parent id>_]<category id>"
   * $index is the 0-based position of the record within its category.
   */
  public static function formatDisplayNumber(Category $category, Archive $archive, int $index): string
  {
    $number = $category->number . '.' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);

    // A register (sub-category) also carries its parent's id
    $acronym = $category->isRegister()
      ? "{$archive->acronym}_{$category->parent->custom_id}_{$category->custom_id}"
      : "{$archive->acronym}_{$category->custom_id}";

    return "{$number} / {$acronym}";
  }

}