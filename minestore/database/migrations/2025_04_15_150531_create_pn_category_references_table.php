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
        Schema::create('pn_category_references', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('internal_category_id');
            $table->string('external_category_id');
            $table->timestamps();

            $table->foreign('internal_category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->index('external_category_id');

            $table->unique(['internal_category_id', 'external_category_id'], 'pn_category_reference_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pn_category_references');
    }
};
