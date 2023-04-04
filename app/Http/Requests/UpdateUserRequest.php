<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Model $user */
        $user = $this->route('user') ?? $this->user();

        $emailValidation = [
            'email' => [
                'required',
                'string',
                'email',
                $user === null
                    ? Rule::unique('users', 'email')
                    : Rule::unique('users', 'email')->ignoreModel($user)
            ],
        ];

        return array_merge([
            'first_name' => ['required', 'string',  'min:2'],
            'last_name' => ['required', 'string',  'min:2'],
            ...$emailValidation,
            'password' => ['required', 'string', 'confirmed'],
            'avatar' => ['nullable', 'uuid', 'exists:files,uuid'],
            'address' => ['required', 'string', 'min:6'],
            'phone_number' => ['required', 'string', 'min:6'],
            'is_marketing' => ['nullable', 'boolean'],
        ], $emailValidation);
    }

    public function authorize(): bool
    {
        return $this->user() !== null;
    }
}
