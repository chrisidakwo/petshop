<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\FileFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class File extends Model
{
    protected $fillable = ['uuid', 'name', 'path', 'size', 'type'];

    protected static function newFactory(): FileFactory|Factory
    {
        return FileFactory::new();
    }
}
