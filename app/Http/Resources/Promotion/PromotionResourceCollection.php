<?php

declare(strict_types=1);

namespace App\Http\Resources\Promotion;

use Illuminate\Http\Request;
use App\Http\Resources\BaseResourceCollection;

/**
 * @see \App\Models\Promotion
 */
class PromotionResourceCollection extends BaseResourceCollection
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [];
    }
}
