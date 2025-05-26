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
        Schema::table('pn_settings', function (Blueprint $table) {
            $table->tinyInteger('enabled')->default(0)->after('id');
            $table->string('api_key', 560)->nullable()->after('store_id');
            $table->tinyInteger('tax_mode')->default(0)->after('api_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pn_settings', function (Blueprint $table) {
            $table->dropColumn('enabled');
            $table->dropColumn('api_key');
            $table->dropColumn('tax_mode');
        });
    }
};
