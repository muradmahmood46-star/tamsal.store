<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('chield_categories') && !Schema::hasColumn('chield_categories', 'vendor_id')) {
            Schema::table('chield_categories', function (Blueprint $table) {
                $table->unsignedBigInteger('vendor_id')->nullable()->index()->after('subcategory_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('chield_categories') && Schema::hasColumn('chield_categories', 'vendor_id')) {
            Schema::table('chield_categories', function (Blueprint $table) {
                $table->dropIndex(['vendor_id']);
                $table->dropColumn('vendor_id');
            });
        }
    }
};
