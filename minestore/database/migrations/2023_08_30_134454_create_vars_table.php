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
        Schema::create('vars', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('name', 45);
			$table->string('identifier', 45);
			$table->string('description', 255);
			$table->tinyInteger('type');
            $table->tinyInteger('deleted')->default(0);
			$table->json('variables')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vars');
    }
};
