<?php

declare(strict_types=1);

namespace App\Models;

class File extends Model
{
    protected $fillable = ['uuid', 'name', 'path', 'size', 'type'];
}
