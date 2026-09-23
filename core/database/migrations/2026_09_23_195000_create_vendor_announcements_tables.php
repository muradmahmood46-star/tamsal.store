<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorAnnouncementsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('vendor_announcements')) {
            Schema::create('vendor_announcements', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->longText('message');
                $table->string('badge_type')->default('info');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('vendor_announcement_views')) {
            Schema::create('vendor_announcement_views', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vendor_id')->index();
                $table->unsignedBigInteger('announcement_id')->index();
                $table->timestamps();
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
        Schema::dropIfExists('vendor_announcement_views');
        Schema::dropIfExists('vendor_announcements');
    }
}
