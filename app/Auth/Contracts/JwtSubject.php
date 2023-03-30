<?php

declare(strict_types=1);

namespace App\Auth\Contracts;

interface JwtSubject
{
    public function getSubjectIdentifier(): string;
}
