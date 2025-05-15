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
        Schema::table('settings', function (Blueprint $table) {
            $table->tinyInteger('discord_bot_enabled')->default(0)->after('discord_url');
            $table->string('discord_client_id')->nullable()->after('discord_bot_enabled');
            $table->string('discord_client_secret')->nullable()->after('discord_client_id');
            $table->string('discord_bot_token')->nullable()->after('discord_client_secret');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('discord_bot_enabled');
            $table->dropColumn('discord_client_id');
            $table->dropColumn('discord_client_secret');
            $table->dropColumn('discord_bot_token');
        });
    }
};
