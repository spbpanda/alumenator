<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\PlayerData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//        Admin::create([
//            'username' => 'testing',
//            'password' => Hash::make('testing'), // remove later
//            'rules'  =>'{"isAdmin": true}'
//        ]);
        if (env('APP_DEBUG', true)) {
            $this->seedTestData();
        }
    }

    private function seedTestData()
    {
        $this->call(SettingsSeeder::class);
        $this->call(PaymentMethodsSeeder::class);
        $this->call(PlayerDataSeeder::class);
        $this->call(BanSeeder::class);
        $this->call(WhitelistSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CartSeeder::class);
        $this->call(PaymentSeeder::class);
    }
}
