<?php

declare(strict_types=1);

namespace App\Http\Parsers;

use Illuminate\Http\Request;

class AuthHeader implements HttpParser
{
    public function parse(Request $request): ?string
    {
        $header = $request->headers->get('authorization');

        if ($header !== null) {
            $position = strripos($header, 'bearer');

            if ($position !== false) {
                $header = substr($header, $position + strlen('bearer'));

                // dd($header);

                return trim(
                    str_contains($header, ',')
                        ? (string) strstr($header, ',', true)
                        : $header
                );
            }
        }

        return null;
    }
}
