<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('discord_role_queue', function (Blueprint $table) {
            $table->id();
            $table->string('discord_id');
            $table->tinyInteger('action')->default(0);
            $table->string('role_id');

            $table->unsignedBigInteger('internal_role_id');
            $table->foreign('internal_role_id')->references('id')->on('discord_roles');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('payment_id')->nullable();
            $table->foreign('payment_id')->references('id')->on('payments');

            $table->tinyInteger('processed')->default(0);
            $table->integer('attempts')->default(0);
            $table->text('error')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discord_role_queue');
    }
};
