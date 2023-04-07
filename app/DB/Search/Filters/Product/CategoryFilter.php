<?php

declare(strict_types=1);

namespace App\DB\Search\Filters\Product;

use Illuminate\Database\Eloquent\Builder;
use Fouladgar\EloquentBuilder\Support\Foundation\Contracts\Filter;

class CategoryFilter extends Filter
{
    /**
     * Apply the condition to the query.
     */
    public function apply(Builder $builder, mixed $value): Builder
    {
        return $builder->where('products.category_uuid', $value)
            ->orWhereHas('category', function ($query) use ($value)  {
                $query->where('categories.name', 'LIKE', "%{$value}%");
            });
    }
}
