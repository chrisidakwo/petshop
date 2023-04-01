<?php

declare(strict_types=1);

namespace App\DB\Search\Filters\User;

use Fouladgar\EloquentBuilder\Support\Foundation\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;

class PhoneFilter extends Filter
{
    /**
     * Apply the condition to the query.
     */
    public function apply(Builder $builder, mixed $value): Builder
    {
        $value = $value === 'null' ? null : $value;

        if (is_null($value)) {
            return $builder->where('users.phone_number', null);
        }

        return $builder->where('users.phone_number', 'LIKE', "%{$value}%");
    }
}
