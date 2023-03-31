<?php

namespace App\Http\Services;

use App\Models\User;
use EloquentBuilder;
use Fouladgar\EloquentBuilder\Exceptions\NotFoundFilterException;
use Illuminate\Contracts\Pagination\Paginator;

class UserService
{
    /**
     * @param array<string, mixed> $fields
     *
     * @return Paginator<User>
     * @throws NotFoundFilterException
     */
    public function list(
        array $fields,
        int $page,
        int $limit,
        string|null $sortColumn,
        bool $sortDesc = true,
    ): Paginator {
        $sortDirection = $sortDesc ? 'desc' : 'asc';

        $userQuery = User::query()->where('users.is_admin', 0)
            ->orderBy($sortColumn ?: 'created_at', $sortDirection);

        return EloquentBuilder::to($userQuery, $fields)->paginate(
            perPage: $limit,
            page: $page,
        );
    }
}
