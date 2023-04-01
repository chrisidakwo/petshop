<?php

declare(strict_types=1);

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

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): User
    {
        return User::query()->create([
            'first_name' => $attributes['first_name'],
            'last_name' => $attributes['last_name'],
            'email' => $attributes['email'],
            'avatar' => $attributes['avatar'] ?? null,
            'address' => $attributes['address'],
            'phone_number' => $attributes['phone_number'],
            'is_marketing' => $attributes['is_marketing'] ?? 0,
            'password' => bcrypt($attributes['password']),
            'is_admin' => $attributes['is_admin'],
        ])->refresh();
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(User $user, array $attributes): User
    {
        $user->fill([
            'first_name' => $attributes['first_name'],
            'last_name' => $attributes['last_name'],
            'email' => $attributes['email'],
            'password' => bcrypt($attributes['password']),
            'avatar' => $attributes['avatar'] ?? $user->avatar,
            'address' => $attributes['address'],
            'phone_number' => $attributes['phone_number'],
            'is_marketing' => $attributes['is_marketing'] ?? $user->is_marketing,
        ])->save();

        return $user->refresh();
    }

    public function delete(User $user): ?bool
    {
        return $user->delete();
    }
}
