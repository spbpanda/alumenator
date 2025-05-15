<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::table('payment_methods')->insert([
            'name' => 'TBank',
            'description' => 'Оплата через TBank',
            'config' => json_encode([
                'terminal_key' => '',
                'secret_key' => '',
                'test' => true
            ]),
            'enable' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function down()
    {
        DB::table('payment_methods')->where('name', 'TBank')->delete();
    }
};
