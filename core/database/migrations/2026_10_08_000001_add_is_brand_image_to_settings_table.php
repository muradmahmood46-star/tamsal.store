<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddIsBrandImageToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                if (!Schema::hasColumn('settings', 'is_brand_image')) {
                    $table->tinyInteger('is_brand_image')->default(1)->nullable()->after('is_brands');
                }
            });
        }

        if (Schema::hasTable('brands')) {
            try {
                DB::statement("ALTER TABLE `brands` MODIFY `photo` VARCHAR(255) NULL");
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                if (Schema::hasColumn('settings', 'is_brand_image')) {
                    $table->dropColumn('is_brand_image');
                }
            });
        }
    }
}
