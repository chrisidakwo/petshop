<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ProductRequest;
use App\Http\Services\ProductService;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Product\ProductResourceCollection;
use Fouladgar\EloquentBuilder\Exceptions\NotFoundFilterException;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="Products API endpoint"
 * )
 */
class ProductController extends Controller
{
    public function __construct(private ProductService $productService)
    {
        $this->middleware(['auth:api'])->only('store', 'update', 'destroy');
    }

    /**
     * List all products
     *
     * @OA\Get(
     *     path="/api/v1/products",
     *     tags={"Products"},
     *     summary="List all products",
     *     operationId="products/index",
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
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="price",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="brand",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *     )
     * )
     *
     * @throws NotFoundFilterException
     */
    public function index(Request $request): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 10);
        $sortBy = $request->get('sortBy');
        $desc = $request->boolean('desc');

        $searchFields = Arr::except($request->all(), ['page', 'limit', 'sortBy', 'desc']);

        $products = $this->productService->list(
            fields: $searchFields,
            page: $page,
            limit: $limit,
            sortColumn: $sortBy,
            sortDesc: $desc,
        );

        return ProductResourceCollection::make($products)->toResponse($request);
    }

    /**
     * Create a new product
     *
     * @OA\Post(
     *     path="/api/v1/product/create",
     *     tags={"Products"},
     *     summary="Create a new product",
     *     operationId="product/create",
     *     security={{"bearerAuth": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"category_uuid", "title", "price", "description", "metadata"},
     *                 @OA\Property(
     *                     property="category_uuid",
     *                     description="Category UUID",
     *                     type="string",
     *                     format="uuid"
     *                 ),
     *                 @OA\Property(
     *                     property="title",
     *                     description="Product title",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     description="Product price",
     *                     type="number",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     description="Product description",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="metadata",
     *                     description="Product metadata",
     *                     type="object",
     *                     @OA\Property(
     *                             property="image",
     *                             description="Image UUID",
     *                             type="string",
     *                             format="uuid"
     *                         ),
     *                         @OA\Property(
     *                             property="brand",
     *                             description="Brand UUID",
     *                             type="string",
     *                             format="uuid"
     *                         ),
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
    public function store(ProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return $this->response([
            'uuid' => $product->uuid,
        ], 201);
    }

    /**
     * Fetch a product
     *
     * @OA\Get(
     *     path="/api/v1/product/{uuid}",
     *     tags={"Products"},
     *     summary="Fetch a product",
     *     operationId="product/show",
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
    public function show(Request $request, Product $product): JsonResponse
    {
        return ProductResource::make($product)->toResponse($request);
    }

    /**
     * Update an existing product
     *
     * @OA\Put(
     *     path="/api/v1/product/{uuid}",
     *     tags={"Products"},
     *     summary="Update an existing product",
     *     operationId="product/update",
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
     *                 required={"category_uuid", "title", "price", "description", "metadata"},
     *                 @OA\Property(
     *                     property="category_uuid",
     *                     description="Category UUID",
     *                     type="string",
     *                     format="uuid"
     *                 ),
     *                 @OA\Property(
     *                     property="title",
     *                     description="Product title",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     description="Product price",
     *                     type="number",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     description="Product description",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="metadata",
     *                     description="Product metadata",
     *                     type="object",
     *                     @OA\Property(
     *                             property="image",
     *                             description="Image UUID",
     *                             type="string",
     *                             format="uuid"
     *                         ),
     *                         @OA\Property(
     *                             property="brand",
     *                             description="Brand UUID",
     *                             type="string",
     *                             format="uuid"
     *                         ),
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
    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $product = $this->productService
            ->update($product, $request->validated())
            ->load(['category', 'brand']);

        return $this->response(ProductResource::make($product)->toArray($request));
    }

    /**
     * Delete an existing product
     *
     * @OA\Delete(
     *     path="/api/v1/product/{uuid}",
     *     tags={"Products"},
     *     summary="Delete an existing product",
     *     operationId="product/delete",
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
    public function destroy(Product $product): JsonResponse
    {
        $result = $this->productService->delete($product);

        if ($result === false) {
            return $this->error('An error occurred!');
        }

        return $this->response();
    }
}
