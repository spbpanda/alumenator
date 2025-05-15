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
        Schema::create('required_items', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('required_item_id');
            $table->timestamps();

            $table->primary(['item_id', 'required_item_id']);
            $table->index('required_item_id', 'required_items_required_item_id');

            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade')->onUpdate('restrict');
            $table->foreign('required_item_id')->references('id')->on('items')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('required_items');
    }
};
