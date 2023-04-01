<?php

namespace App\Http\Requests;

class StoreUserRequest extends UserRequest
{
    /**
     * {@inheritdoc}
     */
    protected function getAvatarRule(): array
    {
        return ['nullable', 'uuid', 'exists:files,uuid'];
    }
}
