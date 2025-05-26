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
            $table->tinyInteger('automatic_reset')->default(0)->after('automatic_disabling');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donation_goals', function (Blueprint $table) {
            $table->dropColumn('automatic_reset');
        });
    }
};
