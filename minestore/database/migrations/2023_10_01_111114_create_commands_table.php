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
        Schema::create('commands', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('item_type');
            $table->integer('item_id');
            $table->text('command');
            $table->tinyInteger('event')->default(0);
            $table->tinyInteger('is_online_required')->default(0);
            $table->tinyInteger('execute_once_on_any_server')->default(0);
            $table->integer('delay_value')->default(0);
            $table->tinyInteger('delay_unit')->default(0);
            $table->integer('repeat_value')->default(0);
            $table->tinyInteger('repeat_unit')->default(0);
            $table->integer('repeat_cycles')->default(0);
            $table->timestamps();

            $table->index(['item_type', 'item_id'], 'item_type_id_idx');
        });

        Schema::table('commands', function (Blueprint $table) {
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
        Schema::dropIfExists('commands');
    }
};
