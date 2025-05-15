<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    private static $user_id = 1;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => self::$user_id++,
            'items' => 0,
            'price' => fake()->randomFloat(2, 1, 1000),
            'clear_price' => fake()->randomFloat(2, 1, 1000),
            'tax' => fake()->randomFloat(2, 1, 10),
            'virtual_price' => fake()->randomFloat(2, 1, 1000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
