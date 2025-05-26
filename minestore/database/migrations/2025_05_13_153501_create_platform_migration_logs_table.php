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
        Schema::create('platform_migration_logs', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->default(0);
            $table->integer('internal_id')->nullable();
            $table->integer('external_id')->nullable();
            $table->unsignedBigInteger('migration_id')->nullable();
            $table->foreign('migration_id')->references('id')->on('platform_migrations')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_migration_logs');
    }
};
