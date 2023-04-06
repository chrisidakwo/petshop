<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Auth\Jwt;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Authenticate extends Middleware
{
    protected Jwt $jwt;

    public function __construct(Auth $auth, Jwt $jwt)
    {
        parent::__construct($auth);

        $this->jwt = $jwt;
    }

    /**
     * @param array<string> $guards
     *
     * @throws AuthenticationException
     */
    protected function authenticate(Request $request, array $guards): void
    {
        if (count($guards) === 0) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if ($guard === 'api' && $this->auth->guard($guard)->user() === null) {
                $token = $this->jwt->parseRequestForToken();

                $this->authenticateUser($token);
                $this->auth->shouldUse($guard);

                return;
            }

            if ($this->auth->guard($guard)->check()) {
                $this->auth->shouldUse((string) $guard);
                return;
            }
        }

        $this->unauthenticated($request, $guards);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    protected function authenticateUser(?string $token): void
    {
        if ($token === null) {
            throw new UnauthorizedHttpException('auth', 'Unauthorized');
        }

        try {
            if (! \Illuminate\Support\Facades\Auth::authenticate()) {
                throw new UnauthorizedHttpException('auth', 'Failed to authenticate user');
            }
        } catch (Exception $ex) {
            throw new UnauthorizedHttpException('auth', 'Failed to authenticate user', $ex);
        }
    }
}
