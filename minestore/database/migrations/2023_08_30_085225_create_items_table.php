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
        Schema::create('items', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
			$table->string('name', 255);
			$table->string('image', 255)->nullable();
			$table->double('price', 255, 2);
			$table->double('discount', 255, 2)->default('0.00');
			$table->double('virtual_price', 255, 2)->nullable();
			$table->double('giftcard_price', 255, 2)->default('0.00');
			$table->longText('description');
			$table->integer('expireAfter')->default('0');
			$table->tinyInteger('expireUnit')->default('0');
			$table->dateTime('publishAt')->nullable();
			$table->dateTime('showUntil')->nullable();
			$table->unsignedBigInteger('category_id')->default('0');
			$table->integer('sorting')->default('1');
			$table->tinyInteger('type')->default('0');
			$table->tinyInteger('req_type')->default('1');
			$table->tinyInteger('featured')->default('0');
			$table->tinyInteger('is_subs')->default('0');
			$table->integer('chargePeriodValue')->default('1');
			$table->integer('chargePeriodUnit')->default('3');
			$table->tinyInteger('is_virtual_currency_only')->default('0');
			$table->tinyInteger('is_any_price')->default('0');
			$table->tinyInteger('active')->default('1');
            $table->tinyInteger('deleted')->default('0');
			$table->tinyInteger('is_server_choice')->default('0');
			$table->string('item_id', 45)->nullable();
			$table->longText('item_lore')->nullable();
			$table->integer('quantityUserLimit')->nullable();
			$table->integer('quantityUserPeriodValue')->default('-1');
			$table->integer('quantityUserPeriodUnit')->default('0');
			$table->integer('quantityGlobalLimit')->nullable();
			$table->integer('quantityGlobalPeriodUnit')->default('0');
			$table->integer('quantityGlobalPeriodValue')->default('-1');
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
        Schema::dropIfExists('items');
    }
};
