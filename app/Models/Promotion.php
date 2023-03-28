<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasUuid;

    protected $fillable = ['uuid', 'title', 'content', 'metadata'];

    protected $casts = [
        'metadata' => 'array',
    ];
}
