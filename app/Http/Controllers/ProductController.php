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

class ProductController extends Controller
{
    public function __construct(private ProductService $productService)
    {
        $this->middleware(['auth:api'])->only('store', 'update', 'destroy');
    }

    /**
     * Display a listing of the resource.
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

    public function store(ProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return $this->response([
            'uuid' => $product->uuid,
        ], 201);
    }

    public function show(Request $request, Product $product): JsonResponse
    {
        return ProductResource::make($product)->toResponse($request);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $product = $this->productService
            ->update($product, $request->validated())
            ->load(['category', 'brand']);

        return $this->response(ProductResource::make($product)->toArray($request));
    }

    /**
     * Remove the specified resource from storage.
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
