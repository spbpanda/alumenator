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
        Schema::create('cart_select_servers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('server_id');

            $table->index('item_id', 'cart_select_servers_item_id_foreign');
            $table->index('server_id', 'cart_select_servers_server_id_foreign');

            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade')->onUpdate('restrict');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade')->onUpdate('restrict');
            $table->foreign('server_id')->references('id')->on('servers')->onDelete('cascade')->onUpdate('restrict');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_select_servers');
    }
};
