<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasUuid;

class ArchiveStructure extends Model
{
  use HasUuid;
  
  protected $table = 'archive_structure';
  
  protected $fillable = [
    'number',
    'name',
    'custom_id',
    'order',
    'numeral_type',
    'custom_id_type',
    'archive_id',
    'parent_id'
  ];

    // Relationships
    public function archive(): BelongsTo
    {
      return $this->belongsTo(Archive::class);
    }

    public function parent(): BelongsTo
    {
      return $this->belongsTo(ArchiveStructure::class, 'parent_id');
    }

    public function children(): HasMany
    {
      return $this->hasMany(ArchiveStructure::class, 'parent_id')->orderBy('order');
    }

    public function registers(): HasMany
    {
      return $this->hasMany(ArchiveStructure::class, 'parent_id')->orderBy('order');
    }

    // Scopes
    public function scopeCategories($query): Builder
    {
      return $query->whereNull('parent_id')->orderBy('order');
    }

    public function scopeRegisters($query): Builder
    {
      return $query->whereNotNull('parent_id')->orderBy('order');
    }
    
    // Helpers
    public function isCategory(): bool
    {
      return $this->parent_id === null;
    }

    public function isRegister(): bool
    {
      return $this->parent_id !== null;
    }
}
