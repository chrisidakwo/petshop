<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileUploadRequest;
use App\Http\Resources\FileResource;
use App\Http\Services\FileService;
use Illuminate\Http\JsonResponse;

class FileController extends Controller
{
    public function __construct(private FileService $fileService)
    { }

    public function upload(FileUploadRequest $request):JsonResponse
    {
        $file = $this->fileService->upload($request->file('file'));

        return FileResource::make($file)->toResponse($request);
    }
}
