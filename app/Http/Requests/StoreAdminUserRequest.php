<?php

namespace App\Http\Requests;

class StoreAdminUserRequest extends UserRequest
{
    /**
     * {@inheritdoc}
     */
    protected function getAvatarRule(): array
    {
        return ['required', 'uuid', 'exists:files,uuid'];
    }
}
