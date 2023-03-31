<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CheckIsAdmin
{
    /**
     * @throws AuthenticationException|AccessDeniedHttpException
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var User|null $user */
        $user = $request->user();

        if (null === $user) {
            throw new AuthenticationException('Failed to authenticate user');
        }

        if ($user->is_admin === 0) {
            throw new AccessDeniedHttpException('Cannot access resource');
        }

        return $next($request);
    }
}
