<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\File;
use App\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * @throws Exception
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_uuid' => Category::factory()->create()->uuid,
            'title' => Str::title($this->faker->words(random_int(4, 8),  true)),
            'price' => $this->faker->randomFloat(2, 28, 880),
            'description' => $this->faker->sentences(9,  true),
            'metadata' => [
                'brand' => Brand::factory()->create()->uuid,
                'image' => File::factory()->type('image/jpeg')->create()->uuid,
            ],
        ];
    }

    public function category(string $uuid): static
    {
        return $this->state(function () use ($uuid) {
            return [
                'category_uuid' => $uuid,
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
