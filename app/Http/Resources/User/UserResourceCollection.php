<?php

declare(strict_types=1);

namespace App\Http\Resources\User;

use App\Http\Resources\BaseResourceCollection;
use Illuminate\Http\Request;

class UserResourceCollection extends BaseResourceCollection
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [];
    }
}
