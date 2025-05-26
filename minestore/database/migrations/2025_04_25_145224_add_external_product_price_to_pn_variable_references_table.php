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
        Schema::table('pn_variable_references', function (Blueprint $table) {
            $table->unsignedBigInteger('external_product_price')->nullable()->after('external_product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pn_variable_references', function (Blueprint $table) {
            $table->dropColumn('external_product_price');
        });
    }
};
