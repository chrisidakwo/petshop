<?php

declare(strict_types=1);

namespace App\Http\Requests;

class StoreAdminUserRequest extends UserRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * {@inheritdoc}
     */
    protected function getAvatarRule(): array
    {
        return ['required', 'uuid', 'exists:files,uuid'];
    }
}
