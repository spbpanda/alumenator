<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'username' => fake()->userName,
            'avatar' => fake()->imageUrl,
            'system' => fake()->word,
            'identificator' => fake()->word,
            'uuid' => fake()->uuid,
            'country' => fake()->country,
            'api_token' => Str::random(32),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
