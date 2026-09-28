<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductFromAndContactNumberToItemsTable extends Migration
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
                if (!Schema::hasColumn('items', 'product_from')) {
                    $table->string('product_from')->nullable()->after('estimated_profit');
                }
                if (!Schema::hasColumn('items', 'contact_number')) {
                    $table->string('contact_number')->nullable()->after('product_from');
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
                if (Schema::hasColumn('items', 'product_from')) {
                    $table->dropColumn('product_from');
                }
                if (Schema::hasColumn('items', 'contact_number')) {
                    $table->dropColumn('contact_number');
                }
            });
        }
    }
}
