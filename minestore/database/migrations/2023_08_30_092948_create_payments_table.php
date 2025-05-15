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
        Schema::create('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
			$table->unsignedBigInteger('user_id');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->unsignedBigInteger('cart_id');
			$table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
			$table->double('price', 255, 2)->default('0.00');
			$table->integer('status')->default('0');
			$table->text('currency');
			$table->integer('ref')->nullable();
			$table->text('details');
			$table->string('ip', 60)->nullable();
			$table->string('gateway', 255)->default('');
			$table->string('transaction', 255)->default('');
            $table->text('note')->nullable();
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
        Schema::dropIfExists('payments');
    }
};
