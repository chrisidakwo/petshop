<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class DeleteUserRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // empty
        ];
    }

    public function authorize(): bool
    {
        /** @var User $user */
        $user = $this->user();

        /** @var User $routeUser */
        $routeUser = $this->route('user');

        return $user->isAdmin()
            && $user->id !== $routeUser->id // Prevent deleting the currently authenticated user
            && $routeUser->email !== 'admin@buckhill.co.uk'; // Prevent deleting the default admin
    }
}
