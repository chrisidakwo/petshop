<?php

declare(strict_types=1);

namespace App\DB\Search\Filters\Promotion;

use Fouladgar\EloquentBuilder\Support\Foundation\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;

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
