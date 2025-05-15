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
        Schema::create('gifts', function (Blueprint $table) {
            $table->unsignedBigInteger('id', 20)->autoIncrement();
			$table->string('name', 255);
			$table->double('start_balance', 255, 2);
			$table->double('end_balance', 255, 2);
			$table->dateTime('expire_at')->nullable()->default(NULL);
			$table->text('note');
            $table->tinyInteger('deleted')->default(0);
            $table->string('owner', 60)->nullable();
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
        Schema::dropIfExists('gifts');
    }
};
