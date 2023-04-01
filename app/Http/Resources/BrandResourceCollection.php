<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * @see \App\Models\Brand
 */
class BrandResourceCollection extends BaseResourceCollection
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [];
    }
}
