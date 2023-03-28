<?php

namespace App\Models;

use App\Models\Traits\HasSlug;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasSlug, HasUuid;

    protected $fillable = ['uuid', 'title',  'slug'];
}
