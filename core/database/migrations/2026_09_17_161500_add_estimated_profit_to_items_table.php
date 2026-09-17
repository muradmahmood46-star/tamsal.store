<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstimatedProfitToItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('items') && !Schema::hasColumn('items', 'estimated_profit')) {
            Schema::table('items', function (Blueprint $table) {
                $table->decimal('estimated_profit', 16, 2)->default(0.00)->after('video');
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
        if (Schema::hasTable('items') && Schema::hasColumn('items', 'estimated_profit')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('estimated_profit');
            });
        }
    }
}
