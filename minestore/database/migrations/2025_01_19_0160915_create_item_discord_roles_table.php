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
        Schema::create('item_discord_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('role_id');

            $table->primary(['item_id', 'role_id']);
            $table->index('role_id', 'item_discord_roles_role_id_foreign');

            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade')->onUpdate('restrict');
            $table->foreign('role_id')->references('id')->on('discord_roles')->onDelete('cascade')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_discord_roles');
    }
};
