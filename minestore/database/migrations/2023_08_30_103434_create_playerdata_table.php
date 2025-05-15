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
        Schema::create('playerdata', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('username')->unique();
			$table->string('uuid', 255)->unique();
			$table->string('prefix', 255)->default('');
			$table->string('suffix', 255)->default('');
			$table->double('balance', 255, 2)->default('0.00');
			$table->string('player_group', 255)->default('0');
            $table->integer('sorting')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('playerdata');
    }
};
