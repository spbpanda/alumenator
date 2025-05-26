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
        Schema::create('pn_history', function (Blueprint $table) {
            $table->id();
            $table->string('event')->nullable();
            $table->tinyInteger('timeline')->default(0);
            $table->tinyInteger('type')->default(0);
            $table->string('message')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();

            $table->index('event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pn_history', function (Blueprint $table) {
            $table->dropIndex(['event']);
        });

        Schema::dropIfExists('pn_history');
    }
};
