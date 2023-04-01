<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;

class StoreAdminUserRequest extends UserRequest
{
    public function authorize(): bool
    {
        /** @var User $user */
        $user = $this->user();

        return $user->isAdmin();
    }

    /**
     * {@inheritdoc}
     */
    protected function getAvatarRule(): array
    {
        return ['required', 'uuid', 'exists:files,uuid'];
    }
}
