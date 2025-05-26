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
        Schema::table('pn_product_references', function (Blueprint $table) {
            $table->unsignedBigInteger('external_package_price')->nullable()->after('external_package_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pn_product_references', function (Blueprint $table) {
            $table->unsignedBigInteger('external_package_price')->nullable()->after('external_package_id');
        });
    }
};
