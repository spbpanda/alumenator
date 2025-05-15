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
        Schema::create('coupons', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
			$table->string('name', 255);
			$table->tinyInteger('type')->default('0');
			$table->double('discount', 255, 2);
			$table->integer('uses')->nullable()->default('0');
			$table->integer('available')->nullable();
			$table->integer('limit_per_user')->nullable()->default('0');
			$table->double('min_basket', 255, 2)->default('0.00');
			$table->tinyInteger('apply_type')->default('0');
			$table->text('note');
            $table->tinyInteger('deleted')->default('0');
			$table->dateTime('start_at')->nullable()->default(NULL);
			$table->dateTime('expire_at')->nullable()->default(NULL);
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
        Schema::dropIfExists('coupons');
    }
};
