<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Http\Resources\BrandResourceCollection;
use App\Http\Services\BrandService;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function __construct(private BrandService $brandService)
    {
    }

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

    public function show(Request $request, Brand $brand): JsonResponse
    {
        return $this->response(BrandResource::make($brand)->toArray($request));
    }

    public function update(UpdateBrandRequest $request, Brand $brand): JsonResponse
    {
        $brand = $this->brandService->update($brand, $request->validated('title'));

        return $this->response(BrandResource::make($brand)->toArray($request));
    }

    public function delete(Brand $brand): JsonResponse
    {
        $result = $this->brandService->delete($brand);

        if ($result === false) {
            $this->error('An error occurred');
        }

        return $this->response();
    }
}
