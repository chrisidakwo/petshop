<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\BaseResourceCollection;
use Illuminate\Http\Request;

/**
 * @see \App\Models\Product
 */
class ProductResourceCollection extends BaseResourceCollection
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // empty
        ];
    }
}
