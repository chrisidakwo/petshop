<?php

namespace App\Models;

use App\Models\Traits\HasSlug;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasSlug, HasUuid;

    protected $fillable = ['uuid', 'title', 'slug'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
