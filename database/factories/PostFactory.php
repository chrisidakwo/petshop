<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => Str::title($this->faker->sentences(4, true)),
            'content' => $this->faker->paragraphs(15, true),
            'metadata' => [
                'author' => $this->faker->name,
                'image' => File::factory()->type('image/png')->create()->uuid,
            ],
        ];
    }
}
