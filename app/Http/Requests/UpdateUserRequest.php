<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string',  'min:2'],
            'last_name' => ['required', 'string',  'min:2'],
            'email' => ['required', 'string',  'email'],
            'password' => ['required', 'string', 'confirmed'],
            'avatar' => ['nullable', 'uuid', 'exists:files,uuid'],
            'address' => ['required', 'string', 'min:6'],
            'phone_number' => ['required', 'string', 'min:6'],
            'is_marketing' => ['nullable', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
