<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class BlogService
{
    public function listPosts(
        int $page,
        int $limit,
        string|null $sortColumn,
        bool $sortDesc = true,
    ): LengthAwarePaginator {
        $sortDirection = $sortDesc ? 'desc' : 'asc';

        return Post::query()
            ->orderBy($sortColumn ? $sortColumn : 'created_at', $sortDirection)
            ->paginate(
                perPage: $limit,
                page: $page,
            );
    }
}
