<?php

declare(strict_types=1);

namespace App\Auth\Contracts;

interface Validator
{
    public function validate(string $token): string;

    public function isValid(string $token): bool;
}
