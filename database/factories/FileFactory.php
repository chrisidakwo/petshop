<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<File>
 */
class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'path' => $this->faker->filePath(),
            'size' => $this->faker->randomNumber(5),
            'type' => $this->faker->randomElement(['image/jpeg', 'video/mp4', 'image/png', 'image/jpg']),
        ];
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function type(string $type): static
    {
        return $this->state(function () use ($type) {
            return [
                'type' => $type,
            ];
        });
    }
}
