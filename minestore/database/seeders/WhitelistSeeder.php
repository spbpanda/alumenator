<?php

namespace Database\Seeders;

use App\Models\Ban;
use App\Models\Whitelist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WhitelistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Whitelist::factory()->count(50)->create();
    }
}
