<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SqlJob extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_PENDING      = 'PENDING';
    const STATUS_IN_PROGRESS  = 'IN_PROGRESS';
    const STATUS_DONE         = 'DONE';
    const STATUS_CANCELLED    = 'CANCELLED';
    const STATUS_ERROR        = 'ERROR';

    protected $casts = [
      'execution_date' => 'datetime:Y-m-d H:i'
    ];

    protected $fillable = [
      'title',
      'description',
      'connection_id',
      'script',
      'execution_date',
      'status',
    ];

    public function connection()
    {
      return $this->belongsTo(Connection::class, 'connection_id');
    }
}
