<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement()->unsigned();
			$table->string('name', 255)->default('');
			$table->string('method', 40)->default('listener');
			$table->string('host', 255)->default('');
			$table->integer('port')->default(0);
			$table->string('password', 255)->default('');
			$table->string('host_websocket', 255)->default('');
			$table->string('port_websocket', 255)->default('');
			$table->string('password_websocket', 255)->default('');
			$table->string('secret_key', 255)->default('');
            $table->tinyInteger('deleted')->default(0);
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
        Schema::dropIfExists('servers');
    }
};
