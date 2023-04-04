<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Model $brand */
        $brand = $this->route('brand');

        return [
            'title' => ['required', 'string', Rule::unique('brands')->ignoreModel($brand)],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
