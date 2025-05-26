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
        Schema::create('pn_alerts', function (Blueprint $table) {
            $table->string('alert_id')->primary();
            $table->string('store_id');
            $table->string('entity_id')->nullable();
            $table->string('status');
            $table->string('type');
            $table->string('custom_title')->nullable();
            $table->text('custom_message')->nullable();
            $table->timestamp('action_required_at')->nullable();
            $table->string('action_link')->nullable();
            $table->boolean('store_visible')->default(true);
            $table->boolean('admin_visible')->default(false);
            $table->string('resolved_by')->nullable();
            $table->timestamps();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pn_alerts');
    }
};
