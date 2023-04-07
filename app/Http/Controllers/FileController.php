<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use App\Http\Services\FileService;
use App\Http\Requests\FileUploadRequest;
use App\Http\Resources\File\FileResource;
use League\Flysystem\FilesystemException;

class FileController extends Controller
{
    public function __construct(private FileService $fileService)
    {
    }

    /**
     * Upload a file
     *
     * @throws FilesystemException
     */
    public function upload(FileUploadRequest $request): JsonResponse
    {
        /** @var UploadedFile $file */
        $file = $request->file('file');

        $file = $this->fileService->upload($file);

        return FileResource::make($file)->toResponse($request);
    }
}
