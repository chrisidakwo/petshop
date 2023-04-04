<?php

declare(strict_types=1);

namespace App\Http\Resources\Category;

use Illuminate\Http\Request;
use App\Http\Resources\BaseResourceCollection;

/**
 * @see \App\Models\Category
 */
class CategoryResourceCollection extends BaseResourceCollection
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
