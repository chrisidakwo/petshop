<?php

declare(strict_types=1);

namespace App\Models;

class Brand extends Model
{
    protected $fillable = ['uuid', 'title',  'slug'];
}
