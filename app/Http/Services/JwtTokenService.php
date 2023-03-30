<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Models\JwtToken;

class JwtTokenService
{
    public function find(string $token): ?JwtToken
    {
        return JwtToken::query()->where('unique_id', $token)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function removeToken(string $token): bool|null
    {
        return JwtToken::query()->where('unique_id', $token)->delete();
    }
}
