<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('store_unblock_requests')) {
            Schema::create('store_unblock_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('seller_id')->nullable()->index();
                $table->string('store_name')->nullable();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->text('message');
                $table->text('admin_reply')->nullable();
                $table->timestamp('admin_replied_at')->nullable();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->string('status')->default('Pending')->index(); // Pending, Replied, Unblocked, Rejected
                $table->tinyInteger('is_seen')->default(0)->index();
                $table->timestamp('admin_seen_at')->nullable();
                $table->timestamp('unblocked_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_unblock_requests');
    }
};
