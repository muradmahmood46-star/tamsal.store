<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReturnPolicyFieldsToItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'is_returnable')) {
                $table->tinyInteger('is_returnable')->default(0)->after('is_custom_rating');
            }
            if (!Schema::hasColumn('items', 'return_days')) {
                $table->integer('return_days')->nullable()->default(14)->after('is_returnable');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'return_days')) {
                $table->dropColumn('return_days');
            }
            if (Schema::hasColumn('items', 'is_returnable')) {
                $table->dropColumn('is_returnable');
            }
        });
    }
}
