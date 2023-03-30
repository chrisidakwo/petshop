<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class InvalidBearerToken extends RuntimeException
{
    public static function fromMessage(
        string $message,
        int $statusCode = Response::HTTP_UNAUTHORIZED,
    ): InvalidBearerToken {
        return new InvalidBearerToken($message, $statusCode);
    }
}
