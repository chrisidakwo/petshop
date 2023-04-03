<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<string>>
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
            'delivery_fee' => ['sometimes', 'numeric'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $products = (array) $this->get('products');

            $uniqueProductsUuids = array_unique(Arr::pluck($products,'product_id'));

            if (count($uniqueProductsUuids) < count($products)) {
                for ($i = 0; $i < count($products); $i++) {
                    $validator->errors()
                        ->add(
                            "products.{$i}.product_id",
                            'Two product entries cannot have the same product_id'
                        );
                }
            }
        });
    }
}
