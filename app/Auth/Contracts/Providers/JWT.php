<?php

declare(strict_types=1);

namespace App\Auth\Contracts\Providers;

use App\Exceptions\JwtException;

interface JWT
{
    /**
     * @param array<string, string> $payload Claims
     *
     * @throws JwtException
     */
    public function encode(array $payload): string;

    /**
     * @return array<string, mixed>
     *
     * @throws JwtException
     */
    public function decode(string $token): array;
}
