<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id', 20)->autoIncrement();
            $table->string('username', 255);
            $table->string('avatar', 255);
            $table->string('system', 255);
            $table->string('identificator', 255);
            $table->string('uuid', 40)->nullable();
            $table->string('country')->nullable();
            $table->string('country_code', 5)->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->string('api_token', 80)->unique()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
