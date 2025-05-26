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
        Schema::create('pn_product_references', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('internal_package_id');
            $table->string('external_package_id');
            $table->timestamps();

            $table->foreign('internal_package_id')->references('id')->on('items')->onDelete('cascade');
            $table->index('external_package_id');

            $table->unique(['internal_package_id', 'external_package_id'], 'pn_product_reference_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pn_products_references');
    }
};
