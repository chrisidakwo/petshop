<?php

namespace App\Http\Services;

use App\Models\Promotion;
use EloquentBuilder;
use Fouladgar\EloquentBuilder\Exceptions\NotFoundFilterException;
use Illuminate\Pagination\LengthAwarePaginator;

class PromotionService
{
    /**
     * @param array<string, mixed> $fields
     *
     * @return LengthAwarePaginator
     * @throws NotFoundFilterException
     */
    public function list(
        array $fields,
        int $page,
        int $limit,
        string|null $sortColumn,
        bool $sortDesc = true,
    ): LengthAwarePaginator {
        $sortDirection = $sortDesc ? 'desc' : 'asc';

        $promotions = Promotion::query()->orderBy($sortColumn ? $sortColumn : 'created_at', $sortDirection);

        return EloquentBuilder::to($promotions, $fields)->paginate(
            perPage: $limit,
            page: $page,
        );
    }
}
