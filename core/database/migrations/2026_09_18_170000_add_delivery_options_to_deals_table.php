<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryOptionsToDealsTable extends Migration
{
    public function up()
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->decimal('delivery_charge', 12, 2)->default(0.00)->after('discounted_price');
            $table->tinyInteger('is_free_delivery')->default(0)->after('delivery_charge');
        });
    }

    public function down()
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn(['delivery_charge', 'is_free_delivery']);
        });
    }
}
