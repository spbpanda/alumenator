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
        Schema::create('comparisons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->tinyInteger('type');
            $table->string('name', 255);
            $table->string('description', 255)->default('')->nullable();
            $table->integer('sorting')->default(0);
            $table->timestamps();

            $table->index('category_id', 'comparisons_category_id_foreign');

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade')->onUpdate('restrict');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comparisons');
    }
};
