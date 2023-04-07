<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Petshop API",
 *      description="API routes for the Petshop coding exercies",
 *      @OA\Contact(
 *          email="chris.idakwo@gmail.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="https://apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Base API URL"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $headers
     */
    public function response(
        array $data = [],
        int $status = Response::HTTP_OK,
        array $headers = [],
    ): JsonResponse {
        return response()->json([
            'success' => 1,
            'data' => $data,
            'error' => null,
            'errors' => [],
            'extra' => [],
        ], $status, $headers, JSON_PRETTY_PRINT);
    }

    /**
     * @param array<string, mixed> $errors
     * @param array<string, mixed> $headers
     */
    public function error(
        string $message,
        array $errors = [],
        int $status = Response::HTTP_INTERNAL_SERVER_ERROR,
        array $headers = [],
    ): JsonResponse {
        return response()->json([
            'success' => 0,
            'data' => [],
            'error' => $message,
            'errors' => $errors,
        ], $status, $headers, JSON_PRETTY_PRINT);
    }
}
