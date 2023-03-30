<?php

declare(strict_types=1);

namespace App\Http\Parsers;

use Illuminate\Http\Request;

interface HttpParser
{
    public function parse(Request $request): ?string;
}
