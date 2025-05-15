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
        Schema::create('item_servers', function (Blueprint $table) {
            $table->tinyInteger('type');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('server_id');
            $table->timestamps();

            $table->primary(['type', 'item_id', 'server_id']);
            $table->index('server_id', 'item_servers_server_id_foreign');

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
        Schema::dropIfExists('item_servers');
    }
};
