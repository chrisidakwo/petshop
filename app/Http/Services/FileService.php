<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Models\File;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Symfony\Component\Mime\MimeTypes;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FilesystemException;

class FileService
{
    /**
     * @throws FilesystemException
     */
    public function upload(UploadedFile $file): File
    {
        if (! Storage::disk('public')->directoryExists('pet-shop')) {
            Storage::disk('public')->createDirectory('pet-shop');
        }

        $filePath = Storage::disk('public')->putFileAs('pet-shop', $file, $file->hashName());

        $mimeType = MimeTypes::getDefault()->guessMimeType($file->path());
        $fileSize = $file->getSize();

        return File::query()->create([
            'name' => Str::random(32),
            'path' => "public/{$filePath}",
            'size' => $fileSize,
            'type' => $mimeType,
        ])->refresh();
    }
}
