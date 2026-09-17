<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTogglesToGlobalPopupSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('global_popup_settings', function (Blueprint $table) {
            $table->tinyInteger('is_whatsapp_enabled')->default(1);
            $table->tinyInteger('is_support_enabled')->default(1);
            $table->tinyInteger('is_tiktok_enabled')->default(1);
            $table->tinyInteger('is_youtube_enabled')->default(1);
            $table->tinyInteger('is_facebook_enabled')->default(1);
            $table->tinyInteger('is_telegram_enabled')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('global_popup_settings', function (Blueprint $table) {
            $table->dropColumn([
                'is_whatsapp_enabled',
                'is_support_enabled',
                'is_tiktok_enabled',
                'is_youtube_enabled',
                'is_facebook_enabled',
                'is_telegram_enabled'
            ]);
        });
    }
}
