<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Models\Brand;
use Illuminate\Pagination\LengthAwarePaginator;

class BrandService
{
    public function list(int $page, int $limit, string|null $sortColumn, bool $sortDesc = false): LengthAwarePaginator
    {
        $sortDirection = $sortDesc ? 'desc' : 'asc';

        return Brand::query()->orderBy($sortColumn ? $sortColumn : 'created_at', $sortDirection)
            ->paginate(
                perPage: $limit,
                page: $page,
            );
    }

    public function update(Brand $brand, string $title): Brand
    {
        $brand->fill([
            'title' => $title,
        ])->save();

        return $brand->refresh();
    }

    public function delete(Brand $brand): ?bool
    {
        return $brand->delete();
    }
}
