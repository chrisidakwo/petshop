<?php

declare(strict_types=1);

namespace App\Http\Resources\OrderStatus;

use App\Models\OrderStatus;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResourceCollection;

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
