<?php

declare(strict_types=1);

namespace App\Models;

class Post extends Model
{
    protected $fillable = ['uuid', 'title', 'slug'];

    protected $casts = [
        'metadata' => 'array',
    ];
}
