<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasUuid;

    protected $fillable = ['uuid', 'name', 'path', 'size', 'type'];
}
