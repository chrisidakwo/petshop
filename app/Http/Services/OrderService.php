<?php

namespace App\Http\Services;

use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderService
{
    public function list(int $page, int $limit, string|null $sortColumn, bool $sortDesc): LengthAwarePaginator
    {
        $sortDirection = $sortDesc ? 'desc' : 'asc';

        return Order::query()
            ->orderBy($sortColumn ? $sortColumn : 'created_at', $sortDirection)
            ->with(['orderStatus', 'user', 'payment'])
            ->paginate(
                perPage: $limit,
                page: $page,
            );
    }
}
