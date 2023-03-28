<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\Promotion;
use Illuminate\Database\Eloquent\Factories\Factory;

class PromotionFactory extends Factory
{
    protected $model = Promotion::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->words(6, true),
            'content' => $this->faker->sentences(3, true),
            'metadata' => [
                'valid_from' => date(
                    'Y-m-d',
                    $this->faker->dateTimeBetween('now', '+1 month')->getTimestamp()
                ),
                'valid_to' => date(
                    'Y-m-d',
                    $this->faker->dateTimeBetween('+2 months', '+3 months')->getTimestamp()
                ),
                'image' => File::factory()->type('image/png')->create()->uuid,
            ],
        ];
    }
}
