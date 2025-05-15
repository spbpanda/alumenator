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
        Schema::create('carts', function (Blueprint $table) {
			$table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->integer('items')->default('0');
            $table->double('price', 255, 2)->default(0.00);
			$table->double('clear_price', 255, 2)->default('0');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->float('tax', 8, 2)->default('0');
			$table->double('virtual_price', 255, 2)->default('0.00');
			$table->unsignedBigInteger('coupon_id')->nullable()->default(NULL);
			$table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            $table->unsignedBigInteger('gift_id')->nullable()->default(NULL);
			$table->foreign('gift_id')->references('id')->on('gifts')->onDelete('cascade');
            $table->double('gift_sum', 255, 2)->default('0');
            $table->integer('referral')->nullable();
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('carts');
    }
};
