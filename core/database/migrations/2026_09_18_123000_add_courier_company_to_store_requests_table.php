<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourierCompanyToStoreRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('store_requests', 'courier_company')) {
            Schema::table('store_requests', function (Blueprint $table) {
                $table->string('courier_company', 255)->nullable()->after('product_types');
            });
        }

        if (Schema::hasTable('sellers') && !Schema::hasColumn('sellers', 'courier_company')) {
            Schema::table('sellers', function (Blueprint $table) {
                $table->string('courier_company', 255)->nullable()->after('product_types');
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
        if (Schema::hasColumn('store_requests', 'courier_company')) {
            Schema::table('store_requests', function (Blueprint $table) {
                $table->dropColumn('courier_company');
            });
        }

        if (Schema::hasTable('sellers') && Schema::hasColumn('sellers', 'courier_company')) {
            Schema::table('sellers', function (Blueprint $table) {
                $table->dropColumn('courier_company');
            });
        }
    }
}
