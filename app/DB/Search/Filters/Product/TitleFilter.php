<?php

declare(strict_types=1);

namespace App\DB\Search\Filters\Product;

use Illuminate\Database\Eloquent\Builder;
use Fouladgar\EloquentBuilder\Support\Foundation\Contracts\Filter;

class TitleFilter extends Filter
{
    /**
     * Apply the condition to the query.
     */
    public function apply(Builder $builder, mixed $value): Builder
    {
        return $builder->where('products.title', 'LIKE', "%{$value}%");
    }
}
