<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class FileUploadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => ['required', File::default()->max(2048)],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
