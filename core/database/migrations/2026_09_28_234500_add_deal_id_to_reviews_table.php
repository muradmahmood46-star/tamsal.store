<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDealIdToReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                if (!Schema::hasColumn('reviews', 'deal_id')) {
                    $table->unsignedBigInteger('deal_id')->nullable()->default(null)->after('item_id')->index();
                }
            });
        }

        if (Schema::hasTable('conversations')) {
            Schema::table('conversations', function (Blueprint $table) {
                if (!Schema::hasColumn('conversations', 'deal_id')) {
                    $table->unsignedBigInteger('deal_id')->nullable()->default(null)->after('item_id')->index();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                if (Schema::hasColumn('reviews', 'deal_id')) {
                    $table->dropColumn('deal_id');
                }
            });
        }

        if (Schema::hasTable('conversations')) {
            Schema::table('conversations', function (Blueprint $table) {
                if (Schema::hasColumn('conversations', 'deal_id')) {
                    $table->dropColumn('deal_id');
                }
            });
        }
    }
}
