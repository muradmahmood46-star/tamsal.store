<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWhatsappFieldsToSettingsTable extends Migration
{
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'whatsapp_enabled')) {
                $table->tinyInteger('whatsapp_enabled')->default(0)->after('is_twilio');
            }
            if (!Schema::hasColumn('settings', 'whatsapp_phone_number_id')) {
                $table->string('whatsapp_phone_number_id')->nullable()->after('whatsapp_enabled');
            }
            if (!Schema::hasColumn('settings', 'whatsapp_access_token')) {
                $table->text('whatsapp_access_token')->nullable()->after('whatsapp_phone_number_id');
            }
            if (!Schema::hasColumn('settings', 'whatsapp_from_number')) {
                $table->string('whatsapp_from_number')->nullable()->after('whatsapp_access_token');
            }
            if (!Schema::hasColumn('settings', 'whatsapp_template_order_confirmed')) {
                $table->string('whatsapp_template_order_confirmed')->default('order_confirmed')->after('whatsapp_from_number');
            }
            if (!Schema::hasColumn('settings', 'whatsapp_template_in_progress')) {
                $table->string('whatsapp_template_in_progress')->default('order_in_progress')->after('whatsapp_template_order_confirmed');
            }
            if (!Schema::hasColumn('settings', 'whatsapp_template_delivered')) {
                $table->string('whatsapp_template_delivered')->default('order_delivered')->after('whatsapp_template_in_progress');
            }
            if (!Schema::hasColumn('settings', 'whatsapp_template_canceled')) {
                $table->string('whatsapp_template_canceled')->default('order_canceled')->after('whatsapp_template_delivered');
            }
        });
    }

    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $cols = [
                'whatsapp_enabled', 'whatsapp_phone_number_id', 'whatsapp_access_token',
                'whatsapp_from_number', 'whatsapp_template_order_confirmed',
                'whatsapp_template_in_progress', 'whatsapp_template_delivered',
                'whatsapp_template_canceled',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}
