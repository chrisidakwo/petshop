<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function response(
        array $data = [], int $status = Response::HTTP_OK, array $headers = []
    ): JsonResponse
    {
        return response()->json([
            'success' => 1,
            'data' => $data,
            'error' => null,
            'errors' => [],
        ], $status, $headers, JSON_PRETTY_PRINT);
    }

    public function error(
        string $message, array $errors = [], int $status = Response::HTTP_INTERNAL_SERVER_ERROR, array $headers = [],
    ): JsonResponse
    {
        return response()->json([
            'success' => 0,
            'data' => [],
            'error' => $message,
            'errors' => $errors,
        ], $status, $headers, JSON_PRETTY_PRINT);
    }
}
