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
        Schema::create('pn_customer_references', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('internal_user_id');
            $table->string('external_user_id');
            $table->timestamps();

            $table->foreign('internal_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('external_user_id');

            $table->unique(['internal_user_id', 'external_user_id'], 'pn_customer_reference_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pn_customer_reference');
    }
};
