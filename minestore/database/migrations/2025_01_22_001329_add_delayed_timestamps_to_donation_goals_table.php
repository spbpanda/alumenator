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
        Schema::table('donation_goals', function (Blueprint $table) {
            $table->tinyInteger('is_enabled')->default(1)->after('status');
            $table->timestamp('start_at')->nullable()->after('reached_at');
            $table->timestamp('disable_at')->nullable()->after('start_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donation_goals', function (Blueprint $table) {
            $table->dropColumn('is_enabled');
            $table->dropColumn('start_at');
            $table->dropColumn('disable_at');
        });
    }
};
