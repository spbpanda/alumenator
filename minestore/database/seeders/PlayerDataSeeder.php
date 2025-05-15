<?php

namespace Database\Seeders;

use App\Models\PlayerData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlayerDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PlayerData::factory()->count(100)->create();
    }
}
