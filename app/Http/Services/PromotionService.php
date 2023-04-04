<?php

declare(strict_types=1);

namespace App\Http\Services;

use EloquentBuilder;
use App\Models\Promotion;
use Illuminate\Pagination\LengthAwarePaginator;
use Fouladgar\EloquentBuilder\Exceptions\NotFoundFilterException;

class PromotionService
{
    /**
     * @param array<string, mixed> $fields
     *
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
