<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['uuid', 'title', 'slug'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
