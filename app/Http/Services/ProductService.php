<?php

declare(strict_types=1);

namespace App\Http\Services;

use EloquentBuilder;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Fouladgar\EloquentBuilder\Exceptions\NotFoundFilterException;

class ProductService
{
    /**
     * @param array<string, mixed> $fields
     *
     * @throws NotFoundFilterException
     */
    public function list(
        array $fields,
        int $page,
        int $limit,
        string|null $sortColumn,
        bool $sortDesc = true,
    ): LengthAwarePaginator {
        $sortDirection = $sortDesc ? 'desc' : 'asc';

        $products = Product::query()
            ->orderBy($sortColumn ? $sortColumn : 'created_at', $sortDirection)
            ->with(['category', 'brand']);

        return EloquentBuilder::to($products, $fields)
            ->paginate(
                perPage: $limit,
                page: $page,
            );
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Product
    {
        return Product::query()->create([
            'title' => $data['title'],
            'category_uuid' => $data['category_uuid'],
            'price' => $data['price'],
            'description' => $data['description'],
            'metadata' => [
                'image' => $data['image'],
                'brand' => $data['brand'],
            ],
        ])->refresh();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(Product $product, array $data): Product
    {
        $product->fill([
            'title' => $data['title'],
            'category_uuid' => $data['category_uuid'],
            'price' => $data['price'],
            'description' => $data['description'],
            'metadata' => [
                'image' => $data['image'],
                'brand' => $data['brand'],
            ],
        ])->save();

        return $product->refresh();
    }

    public function delete(Product $product): ?bool
    {
        return $product->delete();
    }
}
