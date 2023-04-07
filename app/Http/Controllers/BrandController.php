<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Services\BrandService;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\Brand\BrandResource;
use App\Http\Resources\Brand\BrandResourceCollection;

class BrandController extends Controller
{
    public function __construct(private BrandService $brandService)
    {
        $this->middleware(['auth:api'])->only(['update', 'delete']);
    }

    /**
     * List all brands
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
     */
    public function show(Request $request, Brand $brand): JsonResponse
    {
        return $this->response(BrandResource::make($brand)->toArray($request));
    }

    /**
     * Update an existing brand
     */
    public function update(UpdateBrandRequest $request, Brand $brand): JsonResponse
    {
        $brand = $this->brandService->update($brand, $request->validated('title'));

        return $this->response(BrandResource::make($brand)->toArray($request));
    }

    /**
     * Delete an existing brand
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
