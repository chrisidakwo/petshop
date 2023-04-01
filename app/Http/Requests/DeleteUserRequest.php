<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // empty
        ];
    }

    public function authorize(): bool
    {
        return $this->user()->isAdmin()
            && $this->user()->id !== $this->route('user')->id // Prevent deleting the currently authenticated user
            && $this->route('user')->email !== 'admin@buckhill.co.uk'; // Prevent deleting the default admin
    }
}
