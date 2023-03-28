<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * @param Request $request
     */
    protected function unauthenticated($request, AuthenticationException $exception): JsonResponse|Response
    {
        return response()->json([
            'success' => 0,
            'data' => [],
            'error' => $exception->getMessage(),
            'errors' => [],
            'trace' => [],
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param ValidationException $e
     * @param Request $request
     *
     * @return JsonResponse|\Illuminate\Http\Response|Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request): JsonResponse|\Illuminate\Http\Response|Response
    {
        return response()->json([
            'success' => 0,
            'data' => [],
            'error' => $e->getMessage(),
            'errors' => $e->errors(),
            'trace' => [],
        ], $e->status);
    }

    /**
     * @param Request $request
     */
    protected function prepareJsonResponse($request, Throwable $e): JsonResponse
    {
        return response()->json(
            [
                'success' => 0,
                'data' => [],
                'error' => $e instanceof HttpExceptionInterface ? $e->getMessage() : 'Server Error',
                'trace' => config('app.debug')
                    ? collect($e->getTrace())->map(fn($trace) => Arr::except($trace, ['args']))->all()
                    : [],
            ],
            $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500,
            $e instanceof HttpExceptionInterface ? $e->getHeaders() : [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }
}
