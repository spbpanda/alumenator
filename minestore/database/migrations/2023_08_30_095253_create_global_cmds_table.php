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
        Schema::create('global_cmds', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->float('price');
			$table->tinyInteger('is_online')->default('1');
			$table->text('cmd');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('global_cmds');
    }
};
