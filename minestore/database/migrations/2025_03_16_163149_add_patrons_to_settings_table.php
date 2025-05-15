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
            $table->tinyInteger('patrons_enabled')->default(0)->after('developer_mode');
            $table->json('patrons_groups')->default('[]')->after('patrons_enabled');
            $table->text('patrons_description')->nullable()->after('patrons_groups');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('patrons_enabled');
            $table->dropColumn('patrons_groups');
            $table->dropColumn('patrons_description');
        });
    }
};
