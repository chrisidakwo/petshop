<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderStatus extends Model
{
    protected $fillable = ['uuid', 'title'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
