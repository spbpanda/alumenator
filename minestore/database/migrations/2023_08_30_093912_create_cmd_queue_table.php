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
        Schema::create('cmd_queue', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('server_id');
            $table->unsignedBigInteger('commands_history_id')->nullable();
            $table->json('command');
            $table->tinyInteger('pending')->default(1)->nullable();

            $table->index('commands_history_id', 'cmd_queue_command_history_id_foreign');
            $table->index('server_id', 'cmd_queue_server_id_foreign');

            $table->foreign('commands_history_id')->references('id')->on('commands_history')->onDelete('cascade')->onUpdate('restrict');
            $table->foreign('server_id')->references('id')->on('servers')->onDelete('cascade')->onUpdate('restrict');
        });

        Schema::table('cmd_queue', function (Blueprint $table) {
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
        Schema::dropIfExists('cmd_queue');
    }
};
