<?php

declare(strict_types=1);

namespace App\Http\Resources\OrderStatus;

use App\Http\Resources\BaseResourceCollection;
use App\Models\OrderStatus;
use Illuminate\Http\Request;

/**
 * @see OrderStatus
 */
class OrderStatusResourceCollection extends BaseResourceCollection
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
