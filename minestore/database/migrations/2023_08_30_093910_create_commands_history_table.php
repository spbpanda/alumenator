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
        Schema::create('commands_history', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('payment_id')->nullable();
			$table->unsignedBigInteger('item_id')->nullable();
			$table->tinyInteger('type')->default(0);
			$table->text('cmd');
			$table->string('username', 255);
			$table->unsignedBigInteger('server_id');
			$table->tinyInteger('status')->default('1');
			$table->tinyInteger('is_online_required')->default('0');
            $table->tinyInteger('execute_once_on_any_server')->default(0);
            $table->tinyInteger('initiated')->default(0)->nullable();
            $table->string('package_name', 255)->nullable();
			$table->timestamp('created_at')->useCurrent();
			$table->timestamp('updated_at')->useCurrent();
			$table->timestamp('executed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commands_history');
    }
};
