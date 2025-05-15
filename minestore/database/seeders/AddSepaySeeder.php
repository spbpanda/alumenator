<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddSepaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_methods')->updateOrInsert(
            [
                'name' => 'SePay'  // Condition to check for existing record
            ],
            [
                'name' => 'SePay',
                'enable' => '0',
                'can_subs' => '0',
                'config' => json_encode([
                    'bank' => '',
                    'bank_account' => '',
                    'bank_owner' => '',
                    'paycode_prefix' => '',
                    'webhook_apikey' => ''
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }
}
