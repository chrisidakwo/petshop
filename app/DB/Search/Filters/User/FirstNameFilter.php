<?php

declare(strict_types=1);

namespace App\DB\Search\Filters\User;

use Fouladgar\EloquentBuilder\Support\Foundation\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;

class FirstNameFilter extends Filter
{
    public function apply(Builder $builder, mixed $value): Builder
    {
        return $builder->where('users.first_name', 'LIKE', "%{$value}%");
    }
}
