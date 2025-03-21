<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ArchiveUser extends Pivot
{
  public $incrementing = false;

  protected $table = 'archive_user';

  protected $fillable = [
    'user_id',
    'archive_id',
    'added_by',
    'added_at'
  ];

  protected $casts = [
    'added_at' => 'datetime',
  ];


  public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function archive(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(Archive::class);
  }

  public function addedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(User::class, 'added_by');
  }
}
