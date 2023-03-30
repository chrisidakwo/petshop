<?php

declare(strict_types=1);

namespace App\Providers;

use App\Auth\Jwt;
use App\Auth\JwtGuard;
use App\Auth\Providers\JwtProvider;
use App\Http\Parsers\AuthHeader;
use App\Http\Services\JwtTokenService;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class JwtAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        $this->app->bind(\App\Auth\Contracts\Providers\JWT::class, function (Application $app) {
            return new JwtProvider(
                $app['config']->get('auth.jwt.secret'),
                [
                    'private' => $app['config']->get('auth.jwt.private_key'),
                    'public' => $app['config']->get('auth.jwt.public_key'),
                ],
            );
        });

        $this->app->bind(JwtTokenService::class, function (Application $app) {
            return new JwtTokenService($app->make(\App\Auth\Contracts\Providers\JWT::class));
        });

        $this->app->bind(Jwt::class, function (Application $app) {
            return new Jwt(
                $app->make(Request::class),
                [new AuthHeader()],
                $app->make(\App\Auth\Contracts\Providers\JWT::class),
            );
        });

        $this->registerAuthGuard();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // empty
    }

    /**
     * @throws BindingResolutionException
     */
    protected function registerAuthGuard(): void
    {
        Auth::resolved(function (AuthManager $auth): void {
            $auth->extend('jwt', function (Application $app, string $name, array $config) {
                return tap($this->makeGuard($config), function (JwtGuard $guard): void {
                    app()->refresh('request', $guard, 'setRequest');
                });
            });
        });
    }

    /**
     * @param array<string, mixed> $config
     *
     * @throws BindingResolutionException
     */
    protected function makeGuard(array $config): JwtGuard
    {
        return new JwtGuard(
            Auth::createUserProvider($config['provider']), // @phpstan-ignore-line
            $this->app->make('request'),
            $this->app->make(Jwt::class),
            $this->app->make(JwtTokenService::class),
        );
    }
}
