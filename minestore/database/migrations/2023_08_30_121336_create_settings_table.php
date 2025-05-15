<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->unsigned();
			$table->string('site_name', 255)->default('');
			$table->string('site_desc', 255);
			$table->string('serverIP', 255)->default('');
			$table->string('serverPort', 255)->default('');
			$table->string('withdraw_game', 255)->default('minecraft');
			$table->string('webhook_url', 255)->default('');
			$table->text('discord_guild_id');
			$table->text('discord_url');
			$table->text('index_content');
			$table->text('index_deal');
			$table->string('lang', 10)->default('en');
			$table->text('allow_langs');
			$table->string('currency', 10)->default('USD');
			$table->text('allow_currs');
			$table->tinyInteger('is_virtual_currency')->default('0');
			$table->string('virtual_currency', 100)->default('');
			$table->string('virtual_currency_cmd', 250)->default('');
			$table->text('block_1');
			$table->text('block_2');
			$table->text('facebook_link');
			$table->text('instagram_link');
			$table->text('discord_link');
			$table->text('twitter_link');
			$table->text('steam_link');
			$table->text('tiktok_link');
			$table->string('auth_type', 255)->default('username');
			$table->tinyInteger('is_staff_page_enabled')->default('0');
			$table->tinyInteger('is_prefix_enabled')->default('0');
			$table->text('enabled_ranks');
			$table->tinyInteger('is_profile_enable')->default('1');
			$table->string('profile_display_format', 255)->default('{username}');
			$table->tinyInteger('is_profile_sync')->default('0');
			$table->string('group_display_format', 255)->default('{group}');
			$table->tinyInteger('is_group_display')->default('0');
			$table->tinyInteger('is_ref')->default('0');
			$table->tinyInteger('details')->default('0');
			$table->integer('cb_threshold')->default('70');
			$table->integer('cb_period')->default('0');
			$table->tinyInteger('cb_username')->default('1');
			$table->tinyInteger('cb_ip')->default('1');
			$table->integer('cb_bypass')->default('80');
			$table->integer('cb_local')->default('0');
			$table->integer('cb_limit')->default('0');
			$table->integer('cb_limit_period')->default('0');
			$table->tinyInteger('cb_geoip')->default('0');
			$table->text('cb_countries');
			$table->tinyInteger('is_api')->default('1');
			$table->string('api_secret', 100)->default('');
			$table->tinyInteger('smtp_enable')->default('0');
			$table->string('smtp_host', 255)->default('');
			$table->string('smtp_port', 20)->default('');
			$table->tinyInteger('smtp_ssl')->default('1');
			$table->string('smtp_user', 255)->default('');
			$table->string('smtp_pass', 512)->default('');
            $table->string('smtp_from', 255)->default('');
			$table->tinyInteger('enable_globalcmd')->default('0');
			$table->tinyInteger('is_featured')->default('0');
			$table->string('featured_items', 255)->default('');
			$table->tinyInteger('is_featured_offer')->default('0');
			$table->integer('theme')->default('1');
			$table->tinyInteger('is_maintenance')->default('0');
			$table->text('maintenance_ips');
			$table->tinyInteger('is_sale_email_notify')->default('0');
            $table->tinyInteger('share_metrics')->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
