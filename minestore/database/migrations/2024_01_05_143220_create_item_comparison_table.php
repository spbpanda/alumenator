<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('item_comparison', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('comparison_id');
            $table->string('value', 255);

            $table->primary(['item_id', 'comparison_id']);
            $table->index('comparison_id', 'item_comparison_comparison_id_foreign');

            $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('comparison_id')->references('id')->on('comparisons')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_comparison');
    }
};
