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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->default(0);
            $table->string('name', 255);
            $table->string('img', 255)->nullable();
            $table->string('url', 255)->nullable();
            $table->longText('description')->nullable();
            $table->integer('sorting')->default(0);
            $table->tinyInteger('is_enable')->default(1);
            $table->tinyInteger('deleted')->default(0);
            $table->string('gui_item_id', 200)->default('minecraft:chest')->nullable();
            $table->tinyInteger('is_cumulative')->default(0);
            $table->tinyInteger('is_listing')->default(0);
            $table->tinyInteger('is_comparison')->default(0);
            $table->timestamps();

            $table->index('parent_id', 'parent_id_idx');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
