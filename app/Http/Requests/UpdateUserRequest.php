<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var Model $user */
        $user = $this->route('user');

        return [
            'first_name' => ['required', 'string',  'min:2'],
            'last_name' => ['required', 'string',  'min:2'],
            'email' => ['required', 'string',  'email', Rule::unique('users')->ignoreModel($user)],
            'password' => ['required', 'string', 'confirmed'],
            'avatar' => ['nullable', 'uuid', 'exists:files,uuid'],
            'address' => ['required', 'string', 'min:6'],
            'phone_number' => ['required', 'string', 'min:6'],
            'is_marketing' => ['nullable', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return $this->user()->isAdmin() || $this->user()->id === $this->route('user')->id;
    }
}
