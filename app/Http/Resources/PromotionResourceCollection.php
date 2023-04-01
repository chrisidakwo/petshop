<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

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
