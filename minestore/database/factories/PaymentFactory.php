<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    private static $user_id = 1;
    private static $cart_id = 1;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => self::$user_id++,
            'cart_id' => self::$cart_id++,
            'price' => fake()->randomFloat(2,1,1000),
            'status' => random_int(0,4),
            'currency' => 'USD',
            'details' => 'None',
            'gateway' => 'PayPal',
            'transaction' => Str::random(10),
            'note' => fake()->text,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
