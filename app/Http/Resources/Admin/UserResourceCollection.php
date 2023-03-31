<?php

namespace App\Http\Resources\Admin;

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
