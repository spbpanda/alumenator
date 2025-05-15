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
        Schema::create('ref_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('referer', 100)->unique();
			$table->string('code', 100)->unique();
			$table->integer('percent')->default('0');
			$table->tinyInteger('cmd')->default(0);
            $table->tinyInteger('deleted')->default(0);
			$table->json('commands')->default('[]');
            $table->string('server_id', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ref_codes');
    }
};
