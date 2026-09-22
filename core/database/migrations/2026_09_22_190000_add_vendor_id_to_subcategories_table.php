<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subcategories') && !Schema::hasColumn('subcategories', 'vendor_id')) {
            Schema::table('subcategories', function (Blueprint $table) {
                $table->unsignedBigInteger('vendor_id')->nullable()->index()->after('category_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('subcategories') && Schema::hasColumn('subcategories', 'vendor_id')) {
            Schema::table('subcategories', function (Blueprint $table) {
                $table->dropIndex(['vendor_id']);
                $table->dropColumn('vendor_id');
            });
        }
    }
};
