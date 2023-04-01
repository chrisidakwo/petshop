<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class UserRequest extends FormRequest
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
            'first_name' => ['required', 'string',  'min:2'],
            'last_name' => ['required', 'string',  'min:2'],
            'email' => ['required', 'string',  'email', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed'],
            'avatar' => $this->getAvatarRule(),
            'address' => ['required', 'string', 'min:6'],
            'phone_number' => ['required', 'string', 'min:6'],
            'is_marketing' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string>
     */
    abstract protected function getAvatarRule(): array;
}
