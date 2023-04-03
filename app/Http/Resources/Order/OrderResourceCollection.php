<?php

declare(strict_types=1);

namespace App\Http\Resources\Order;

use App\Http\Resources\BaseResourceCollection;
use App\Models\Order;
use Illuminate\Http\Request;

/**
 * @see Order
 */
class OrderResourceCollection extends BaseResourceCollection
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
