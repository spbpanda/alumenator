<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PayNowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pn_settings')->insert([
            'id' => 1,
            'variable_tag_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
