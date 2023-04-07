<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Brand;
use Fouladgar\EloquentBuilder\Exceptions\NotFoundFilterException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Services\BrandService;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\Brand\BrandResource;
use App\Http\Resources\Brand\BrandResourceCollection;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Brands",
 *     description="Brands API endpoint"
 * )
 */
class BrandController extends Controller
{
    public function __construct(private BrandService $brandService)
    {
        $this->middleware(['auth:api'])->only(['update', 'delete']);
    }

    /**
     * List all brands
     *
     * @OA\Get(
     *     path="/api/v1/brands",
     *     tags={"Brands"},
     *     summary="List all brands",
     *     operationId="brands/index",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="desc",
     *         in="query",
     *         @OA\Schema(
     *             type="boolean",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $sortBy = $request->get('sortBy');
        $desc = $request->boolean('desc');

        $brands = $this->brandService->list(
            page: $page,
            limit: $limit,
            sortColumn: $sortBy,
            sortDesc: $desc,
        );

        return BrandResourceCollection::make($brands)->toResponse($request);
    }

    /**
     * Fetch a brand
     *
     * @OA\Get(
     *     path="/api/v1/brand/{uuid}",
     *     tags={"Brands"},
     *     summary="Fetch a brand",
     *     operationId="brands/show",
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
     */
    public function show(Request $request, Brand $brand): JsonResponse
    {
        return $this->response(BrandResource::make($brand)->toArray($request));
    }

    /**
     * Update an existing brand
     *
     * @OA\Put(
     *     path="/api/v1/brand/{uuid}",
     *     tags={"Brands"},
     *     summary="Update an existing brand",
     *     operationId="brand/update",
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"title"},
     *                 @OA\Property(
     *                     property="title",
     *                     description="Brand title",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     * )
     */
    public function update(UpdateBrandRequest $request, Brand $brand): JsonResponse
    {
        $brand = $this->brandService->update($brand, $request->validated('title'));

        return $this->response(BrandResource::make($brand)->toArray($request));
    }

    /**
     * Delete an existing brand
     *
     * @OA\Delete(
     *     path="/api/v1/brand/{uuid}",
     *     tags={"Brands"},
     *     summary="Delete an existing brand",
     *     operationId="brand/delete",
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     * )
     */
    public function delete(Brand $brand): JsonResponse
    {
        $result = $this->brandService->delete($brand);

        if ($result === false) {
            $this->error('An error occurred');
        }

        return $this->response();
    }
}
