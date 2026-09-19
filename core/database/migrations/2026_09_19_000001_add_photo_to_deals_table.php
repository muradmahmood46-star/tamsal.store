<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhotoToDealsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('deals', 'photo')) {
            Schema::table('deals', function (Blueprint $table) {
                $table->string('photo')->nullable()->after('slug');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('deals', 'photo')) {
            Schema::table('deals', function (Blueprint $table) {
                $table->dropColumn('photo');
            });
        }
    }
}
