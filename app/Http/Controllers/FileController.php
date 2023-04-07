<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use App\Http\Services\FileService;
use App\Http\Requests\FileUploadRequest;
use App\Http\Resources\File\FileResource;
use League\Flysystem\FilesystemException;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="File",
 *     description="File API endpoint"
 * )
 */
class FileController extends Controller
{
    public function __construct(private FileService $fileService)
    {
    }

    /**
     * Upload a file
     *
     * @OA\Post(
     *     path="/api/v1/file/upload",
     *     tags={"File"},
     *     summary="Upload a file",
     *     operationId="file/upload",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"file"},
     *                 @OA\Property(
     *                     property="file",
     *                     description="File to upload",
     *                     type="string",
     *                     format="binary",
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     * )
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
