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
        Schema::create('item_vars', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('var_id');

            $table->primary(['item_id', 'var_id']);
            $table->index('var_id', 'item_vars_var_id_foreign');

            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade')->onUpdate('restrict');
            $table->foreign('var_id')->references('id')->on('vars')->onDelete('cascade')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_vars');
    }
};
