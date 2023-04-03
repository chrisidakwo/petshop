<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'order_status_uuid' => ['required', 'uuid', 'exists:order_statuses,uuid'],
            'payment_uuid' => ['required', 'uuid', 'exists:payments,uuid'],
            'products' => ['required', 'array'],
            'products.*.product_id' => ['required', 'uuid', 'exists:products,uuid'],
            'products.*.quantity' => ['required', 'numeric', 'gte:1'],
            'billing' => ['required', 'string'],
            'shipping' => ['required', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
