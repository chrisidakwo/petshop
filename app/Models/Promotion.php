<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = ['uuid', 'title', 'content', 'metadata'];

    protected $casts = [
        'metadata' => 'array',
    ];
}
