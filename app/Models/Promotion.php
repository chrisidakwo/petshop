<?php

declare(strict_types=1);

namespace App\Models;

class Promotion extends Model
{
    protected $fillable = ['uuid', 'title', 'content', 'metadata'];

    protected $casts = [
        'metadata' => 'array',
    ];
}
