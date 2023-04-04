<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $uniqueProductTitle = 'unique:products,title';

        /** @var Model $product */
        $product = $this->route('product');

        if ($product !== null) {
            $uniqueProductTitle = Rule::unique('products', 'title')->ignoreModel($product);
        }

        return [
            'title' => ['required', 'string', $uniqueProductTitle],
            'category_uuid' => ['required', 'uuid', 'exists:categories,uuid'],
            'price' => ['required', 'numeric'],
            'description' => ['required', 'string'],
            'image' => ['required', 'uuid', 'exists:files,uuid'],
            'brand' => ['required', 'uuid', 'exists:brands,uuid'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
