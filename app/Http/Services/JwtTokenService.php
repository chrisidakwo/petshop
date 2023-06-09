<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Models\User;
use DateTimeImmutable;
use App\Models\JwtToken;
use App\Exceptions\JwtException;
use App\Auth\Contracts\JwtSubject;
use App\Auth\Contracts\Providers\JWT;
use Illuminate\Contracts\Auth\Authenticatable;

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
            ->latest('created_at')
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
            'user_id' => $user->id, // @phpstan-ignore-line
            'unique_id' => $token,
            'token_title' => "Token for {$user->first_name} {$user->last_name}", // @phpstan-ignore-line
            'restrictions' => [],
            'permissions' => [],
            'expires_at' => $expiresAt->getTimestamp(),
        ]);
    }

    public function updateLastUsed(string $token): void
    {
        $this->find($token)?->update([
            'last_used_at' => now(),
        ]);
    }

    public function expireUserToken(int $userId): bool|int
    {
        return JwtToken::query()->whereUserId($userId)->update([
            'expires_at' => now()->subMinute(),
        ]);
    }
}
