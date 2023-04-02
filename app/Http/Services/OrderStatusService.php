<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Models\OrderStatus;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderStatusService
{
    public function list(int $page, int $limit, string|null $sortColumn, bool $sortDesc): LengthAwarePaginator
    {
        $sortDirection = $sortDesc ? 'desc' : 'asc';

        return OrderStatus::query()
            ->orderBy($sortColumn ? $sortColumn : 'created_at', $sortDirection)
            ->paginate(
                perPage: $limit,
                page: $page,
            );
    }
}
