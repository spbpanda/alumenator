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
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('name', 255);
            $table->double('discount', 255, 2);
            $table->tinyInteger('apply_type')->default('0');
            $table->json('packages_commands');
            $table->double('min_basket', 255, 2)->default('0.00');
			$table->dateTime('start_at');
			$table->dateTime('expire_at');
			$table->tinyInteger('is_enable')->default('1');
            $table->tinyInteger('is_advert')->default('0');
            $table->string('advert_title', 255)->nullable()->default('');
            $table->longText('advert_description')->nullable();
            $table->string('button_name', 255)->nullable()->default('');
            $table->string('button_url', 255)->nullable()->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
};
