<?php

declare(strict_types=1);

namespace App\DB\Search\Filters\Promotion;

use Illuminate\Database\Eloquent\Builder;
use Fouladgar\EloquentBuilder\Support\Foundation\Contracts\Filter;

class ValidFilter extends Filter
{
    public function apply(Builder $builder, mixed $value): Builder
    {
        return $builder->when($value === true, function (Builder $query) {
            return $query->where('promotions.metadata->valid_from', '<=', now())
                ->where('promotions.metadata->valid_to', '>', now());
        }, function (Builder $query) {
            return $query->where('promotions.metadata->valid_to', '<=', now());
        });
    }
}
