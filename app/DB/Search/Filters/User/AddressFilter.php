<?php

declare(strict_types=1);

namespace App\DB\Search\Filters\User;

use Illuminate\Database\Eloquent\Builder;
use Fouladgar\EloquentBuilder\Support\Foundation\Contracts\Filter;

class AddressFilter extends Filter
{
    /**
     * Apply the condition to the query.
     */
    public function apply(Builder $builder, mixed $value): Builder
    {
        return $builder->where('users.address', 'LIKE', "%{$value}%");
    }
}
