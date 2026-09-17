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
        // 1. Add is_seller_blocked column to users table
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'is_seller_blocked')) {
                    $table->tinyInteger('is_seller_blocked')->default(0)->after('is_seller');
                }
            });
        }

        // 2. Add seller_status column to store_requests table
        if (Schema::hasTable('store_requests')) {
            Schema::table('store_requests', function (Blueprint $table) {
                if (!Schema::hasColumn('store_requests', 'seller_status')) {
                    $table->string('seller_status')->default('Active')->after('status');
                }
            });
        }

        // 3. Add is_hidden_by_block column to items table
        if (Schema::hasTable('items')) {
            Schema::table('items', function (Blueprint $table) {
                if (!Schema::hasColumn('items', 'is_hidden_by_block')) {
                    $table->tinyInteger('is_hidden_by_block')->default(0)->after('status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'is_seller_blocked')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_seller_blocked');
            });
        }

        if (Schema::hasTable('store_requests') && Schema::hasColumn('store_requests', 'seller_status')) {
            Schema::table('store_requests', function (Blueprint $table) {
                $table->dropColumn('seller_status');
            });
        }

        if (Schema::hasTable('items') && Schema::hasColumn('items', 'is_hidden_by_block')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('is_hidden_by_block');
            });
        }
    }
};
