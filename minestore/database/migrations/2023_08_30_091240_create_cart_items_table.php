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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->unsignedBigInteger('cart_id');
			$table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
			$table->unsignedBigInteger('item_id');
			$table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->tinyInteger('payment_type')->default('0');
            $table->tinyInteger('is_promoted')->default('0');
            $table->tinyInteger('coupon_applied')->default('0');
            $table->double('price', 16, 2);
            $table->double('initial_price', 16, 2)->nullable();
            $table->double('variable_price', 16, 2)->nullable();
			$table->integer('count')->default('1');
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
        Schema::dropIfExists('cart_items');
    }
};
