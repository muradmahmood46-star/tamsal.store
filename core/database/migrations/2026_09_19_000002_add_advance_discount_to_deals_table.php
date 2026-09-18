<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdvanceDiscountToDealsTable extends Migration
{
    public function up()
    {
        Schema::table('deals', function (Blueprint $table) {
            if (!Schema::hasColumn('deals', 'advance_discount')) {
                $table->decimal('advance_discount', 12, 2)->default(0.00)->after('delivery_charge');
            }
        });
    }

    public function down()
    {
        Schema::table('deals', function (Blueprint $table) {
            if (Schema::hasColumn('deals', 'advance_discount')) {
                $table->dropColumn('advance_discount');
            }
        });
    }
}
