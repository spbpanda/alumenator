<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('item_servers', function (Blueprint $table) {
            if (!Schema::hasColumn('item_servers', 'cmd_id')) {
                $table->unsignedBigInteger('cmd_id')->nullable()->after('item_id');
                $table->foreign('cmd_id')->references('id')->on('commands')->onDelete('cascade');
            }

            $primaryExists = Schema::hasTable('item_servers') && Schema::hasColumn('item_servers', 'type') &&
                Schema::hasColumn('item_servers', 'server_id') &&
                Schema::hasColumn('item_servers', 'item_id');

            if ($primaryExists) {
                $table->dropPrimary(['type', 'item_id', 'server_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('item_servers', function (Blueprint $table) {
            $table->dropPrimary(['type', 'server_id']);
            $table->primary(['type', 'server_id', 'item_id']);
        });
    }
};
