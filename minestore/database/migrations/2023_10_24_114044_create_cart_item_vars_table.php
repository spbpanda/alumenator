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
        Schema::create('cart_item_vars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_item_id');
            $table->unsignedBigInteger('var_id');
            $table->string('var_value', 45);

            $table->unique(['cart_item_id', 'var_id'], 'var_item_cart_item_id_var_id_unique');
            $table->index('var_id', 'cart_item_vars_vars_id_foreign');

            $table->foreign('cart_item_id')->references('id')->on('cart_items')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('var_id')->references('id')->on('vars')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_item_vars');
    }
};
