<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Brand\BrandResource;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 */
class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'category_uuid' => $this->category_uuid,
            'title' => $this->title,
            'price' => $this->price,
            'description' => $this->description,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'category' => new CategoryResource($this->whenLoaded('category')),
            'brand' => new BrandResource($this->whenLoaded('brand')),
        ];
    }
}
