<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
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
