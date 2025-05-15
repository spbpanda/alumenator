<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlayerData>
 */
class PlayerDataFactory extends Factory
{
    const GROUPS = ['Administrator', 'Moderator', 'Player'];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'username' => $this->faker->userName,
            'uuid' => $this->faker->uuid,
            'prefix' => $this->faker->word,
            'suffix' => $this->faker->word,
            'balance' => $this->faker->randomFloat(2, 0, 10000),
            'player_group' => $this->faker->randomElement(self::GROUPS)
        ];
    }
}
