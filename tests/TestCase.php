<?php

namespace Tests;

use App\Auth\Providers\JwtProvider;
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

    protected function getTestJwtProvider(): JwtProvider
    {
        return new JwtProvider(
            $this->getTestSecretKey(),
            [
                'private' => $this->getTestPrivateKey(),
                'public' => $this->getTestPublicKey(),
            ]
        );
    }

    protected function getTestPrivateKey(): string
    {
        return base_path('tests/Keys/private.pem');
    }

    protected function getTestPublicKey(): string
    {
        return base_path('tests/Keys/public.pem');
    }

    protected function getTestSecretKey(): string
    {
        return 'tNLBusVcRts2Wq4YN94a30uG6g7VvOQwInrrsnvnTMTWYZx9MxdxiPG0ArDM7euY';
    }
}
