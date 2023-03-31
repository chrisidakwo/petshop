<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @param array<string, mixed> $overrides
     */
    protected function createPredictableRegularUser(array $overrides = []): User
    {
        $attributes = array_merge([
            'email' => 'test@email.com'
        ], $overrides);

        return User::factory()->create($attributes)->refresh();
    }

    /**
     * @param array<string, mixed> $overrides
     */
    protected function createPredictableAdminUser(array $overrides = []): User
    {
        $attributes = array_merge([
            'email' => 'test@email.com',
            'password' => bcrypt('admin'),
            'is_admin' => 1,
        ], $overrides);

        return User::factory()->create($attributes)->refresh();
    }
}
