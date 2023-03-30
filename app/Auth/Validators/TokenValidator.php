<?php

declare(strict_types=1);

namespace App\Auth\Validators;

use App\Auth\Contracts\Validator;
use App\Exceptions\InvalidBearerToken;
use Exception;

class TokenValidator implements Validator
{
    public function isValid(string $token): bool
    {
        try {
            $this->validate($token);
            return true;
        } catch (Exception) {
            return false;
        }
    }

    public function validate(string $token): string
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw InvalidBearerToken::fromMessage('Wrong number of segments');
        }

        $parts = array_filter(array_map('trim', $parts));

        if (count($parts) !== 3 || implode('.', $parts) !== $token) {
            throw InvalidBearerToken::fromMessage('Malformed token');
        }

        return $token;
    }
}
