<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
      'name',
      'type',
      'host',
      'port',
      'database',
      'user',
      'password'
    ];
}
