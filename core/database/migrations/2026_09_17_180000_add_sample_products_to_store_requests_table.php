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
                if (!Schema::hasColumn('store_requests', 'sample_product_1_name')) {
                    $table->string('sample_product_1_name')->nullable()->after('store_documents');
                }
                if (!Schema::hasColumn('store_requests', 'sample_product_1_image')) {
                    $table->string('sample_product_1_image')->nullable()->after('sample_product_1_name');
                }
                if (!Schema::hasColumn('store_requests', 'sample_product_2_name')) {
                    $table->string('sample_product_2_name')->nullable()->after('sample_product_1_image');
                }
                if (!Schema::hasColumn('store_requests', 'sample_product_2_image')) {
                    $table->string('sample_product_2_image')->nullable()->after('sample_product_2_name');
                }
                if (!Schema::hasColumn('store_requests', 'sample_product_3_name')) {
                    $table->string('sample_product_3_name')->nullable()->after('sample_product_2_image');
                }
                if (!Schema::hasColumn('store_requests', 'sample_product_3_image')) {
                    $table->string('sample_product_3_image')->nullable()->after('sample_product_3_name');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('store_requests')) {
            Schema::table('store_requests', function (Blueprint $table) {
                $cols = [
                    'sample_product_1_name',
                    'sample_product_1_image',
                    'sample_product_2_name',
                    'sample_product_2_image',
                    'sample_product_3_name',
                    'sample_product_3_image'
                ];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('store_requests', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
