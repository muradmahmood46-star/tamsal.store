<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRatingManagementFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('items')) {
            Schema::table('items', function (Blueprint $table) {
                if (!Schema::hasColumn('items', 'is_custom_rating')) {
                    $table->tinyInteger('is_custom_rating')->default(0)->after('stock');
                }
                if (!Schema::hasColumn('items', 'custom_rating')) {
                    $table->decimal('custom_rating', 3, 2)->nullable()->default(5.00)->after('is_custom_rating');
                }
                if (!Schema::hasColumn('items', 'custom_rating_count')) {
                    $table->integer('custom_rating_count')->nullable()->default(0)->after('custom_rating');
                }
            });
        }

        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                if (!Schema::hasColumn('reviews', 'customer_name')) {
                    $table->string('customer_name')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('reviews', 'is_admin_added')) {
                    $table->tinyInteger('is_admin_added')->default(0)->after('customer_name');
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
        if (Schema::hasTable('items')) {
            Schema::table('items', function (Blueprint $table) {
                if (Schema::hasColumn('items', 'is_custom_rating')) {
                    $table->dropColumn('is_custom_rating');
                }
                if (Schema::hasColumn('items', 'custom_rating')) {
                    $table->dropColumn('custom_rating');
                }
                if (Schema::hasColumn('items', 'custom_rating_count')) {
                    $table->dropColumn('custom_rating_count');
                }
            });
        }

        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                if (Schema::hasColumn('reviews', 'customer_name')) {
                    $table->dropColumn('customer_name');
                }
                if (Schema::hasColumn('reviews', 'is_admin_added')) {
                    $table->dropColumn('is_admin_added');
                }
            });
        }
    }
}
