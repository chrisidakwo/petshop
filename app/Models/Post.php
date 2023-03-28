<?php

namespace App\Models;

use App\Models\Traits\HasSlug;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasUuid, HasSlug;

    protected $fillable = ['uuid', 'title', 'slug'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function testing()
    {
        $this->getDirty();
    }
}
