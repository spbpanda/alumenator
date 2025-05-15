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
        Schema::create('themes', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->integer('theme');
			$table->string('name', 50);
			$table->text('description');
			$table->string('img', 255);
			$table->string('url', 255)->default('');
			$table->string('author', 255)->default('');
			$table->tinyInteger('is_custom');
			$table->string('version', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('themes');
    }
};
