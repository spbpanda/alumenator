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
        Schema::create('donation_goals', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->tinyInteger('status')->default('0');
            $table->tinyInteger('automatic_disabling')->default('0');
            $table->double('current_amount', 255, 2);
            $table->double('goal_amount', 255, 2);
            $table->tinyInteger('cmdExecute')->default('0');
            $table->json('commands_to_execute');
            $table->json('servers');
            $table->timestamp('reached_at')->nullable()->default(NULL);
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
        Schema::dropIfExists('donation_goals');
    }
};
