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
        Schema::create('pn_vat_rates', function (Blueprint $table) {
            $table->string('country_code')->primary();
            $table->string('country_name', 100);
            $table->decimal('vat_rate', 4, 2);
            $table->timestamps();

            $table->index('country_name', 'pn_vat_rates_country_name_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pn_vat_rates');
    }
};
