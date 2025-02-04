<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ArchiveUser extends Model
{
  protected $table = 'archive_user';

  public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function archive(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(Archive::class);
  }
}
