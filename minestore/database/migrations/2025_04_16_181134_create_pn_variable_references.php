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
        Schema::create('pn_variable_references', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('variable_id');
            $table->string('value');
            $table->string('external_product_id')->nullable();
            $table->timestamps();

            $table->foreign('variable_id')->references('id')->on('vars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pn_variable_references');
    }
};
