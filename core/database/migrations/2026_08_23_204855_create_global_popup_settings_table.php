<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateGlobalPopupSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_popup_settings', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('is_enabled')->default(0);
            $table->longText('message')->nullable();
            $table->string('whatsapp_link')->nullable();
            $table->string('support_link')->nullable();
            $table->string('tiktok_link')->nullable();
            $table->string('youtube_link')->nullable();
            $table->string('facebook_link')->nullable();
            $table->string('telegram_link')->nullable();
            $table->timestamps();
        });

        // Insert default row
        DB::table('global_popup_settings')->insert([
            'is_enabled' => 0,
            'message' => 'Welcome to our exclusive platform!',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('global_popup_settings');
    }
}
