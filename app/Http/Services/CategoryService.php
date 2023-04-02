<?php

namespace App\Http\Services;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService
{
    public function list(int $page, int $limit, string|null $sortColumn, bool $sortDesc): LengthAwarePaginator
    {
        $sortDirection = $sortDesc ? 'desc' : 'asc';

        return Category::query()
            ->orderBy($sortColumn ? $sortColumn : 'created_at', $sortDirection)
            ->paginate(
                perPage: $limit,
                page: $page,
            );
    }
}
