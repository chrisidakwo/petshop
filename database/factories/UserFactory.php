<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'avatar' => File::factory()->type('image/jpeg')->create()->uuid,
            'address' => $this->faker->streetAddress(),
            'email_verified_at' => now(),
            'password' => bcrypt('userpassword'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function isAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'first_name' => 'Admin',
            'last_name' => 'Buckhill',
            'email' => 'admin@buckhill.co.uk',
            'password' => bcrypt('admin'),
            'is_admin' => 1,
        ]);
    }

    public function phone(): static
    {
        return $this->state(fn () => [
            'phone_number' => $this->faker->phoneNumber(),
        ]);
    }


    public function lastLogin(Carbon $time): static
    {
        return $this->state(function () use ($time) {
            return [
                'last_login_at' => $time,
            ];
        });
    }
}
