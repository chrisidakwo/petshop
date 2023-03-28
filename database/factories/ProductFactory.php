<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\File;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory()->create()->id,
            'title' => $this->faker->word(),
            'price' => $this->faker->randomFloat(2, ),
            'description' => $this->faker->text(),
            'metadata' => [
                'brand' => Brand::factory()->create()->uuid,
                'image' => File::factory()->type('image/jpeg')->create()->uuid,
            ],
        ];
    }

    public function category(int $id): static
    {
        return $this->state(function () use ($id) {
            return [
                'category_id' => $id,
            ];
        });
    }

    public function deleted(): static
    {
        return $this->state(fn () => [
            'deleted_at' => now(),
        ]);
    }
}
