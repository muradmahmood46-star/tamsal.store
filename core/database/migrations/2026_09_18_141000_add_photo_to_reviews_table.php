<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhotoToReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('reviews', 'photo')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->string('photo')->nullable()->after('subject');
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
        if (Schema::hasColumn('reviews', 'photo')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropColumn('photo');
            });
        }
    }
}
