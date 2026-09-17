<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('store_requests')) {
            Schema::table('store_requests', function (Blueprint $table) {
                if (!Schema::hasColumn('store_requests', 'product_types')) {
                    $table->text('product_types')->nullable()->after('shop_address');
                }
            });
        }

        if (Schema::hasTable('sellers')) {
            Schema::table('sellers', function (Blueprint $table) {
                if (!Schema::hasColumn('sellers', 'product_types')) {
                    $table->text('product_types')->nullable()->after('shop_address');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('store_requests')) {
            Schema::table('store_requests', function (Blueprint $table) {
                if (Schema::hasColumn('store_requests', 'product_types')) {
                    $table->dropColumn('product_types');
                }
            });
        }

        if (Schema::hasTable('sellers')) {
            Schema::table('sellers', function (Blueprint $table) {
                if (Schema::hasColumn('sellers', 'product_types')) {
                    $table->dropColumn('product_types');
                }
            });
        }
    }
};
