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
        Schema::create('pn_server_references', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('internal_server_id');
            $table->string('external_server_id');
            $table->timestamps();

            $table->foreign('internal_server_id')->references('id')->on('servers')->onDelete('cascade');
            $table->index('external_server_id');

            $table->unique(['internal_server_id', 'external_server_id'], 'pn_server_reference_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pn_server_references_table');
    }
};
