<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Auth\Contracts\JwtSubject;
use App\Auth\Contracts\Providers\JWT;
use App\Exceptions\JwtException;
use App\Models\JwtToken;
use App\Models\User;
use DateTimeImmutable;
use Illuminate\Auth\Authenticatable;

class JwtTokenService
{
    protected JWT $jwtProvider;

    public function __construct(JWT $jwtProvider)
    {
        $this->jwtProvider = $jwtProvider;
    }

    public function find(string $token): ?JwtToken
    {
        return JwtToken::query()->where('unique_id', $token)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function removeToken(string $token): bool|int|null
    {
        return JwtToken::query()->where('unique_id', $token)->delete();
    }

    /**
     * @param JwtSubject|Authenticatable|User $user
     *
     * @throws JwtException
     */
    public function create(string $token, mixed $user): JwtToken
    {
        $decodedToken = $this->jwtProvider->decode($token);

        /** @var DateTimeImmutable $expiresAt */
        $expiresAt = $decodedToken['exp'];

        return JwtToken::query()->create([
            'user_id' => $user->getAuthIdentifier(),
            'unique_id' => $token,
            'token_title' => "Token for $user->name",
            'restrictions' => [],
            'permissions' => [],
            'expires_at' => $expiresAt->getTimestamp(),
        ]);
    }
}
