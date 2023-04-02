<?php

declare(strict_types=1);

namespace App\Http\Resources\Brand;

use App\Http\Resources\BaseResourceCollection;
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
