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
        if (!Schema::hasColumn('items', 'item_variants')) {
            Schema::table('items', function (Blueprint $table) {
                $table->longText('item_variants')->nullable()->after('stock');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('items', 'item_variants')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('item_variants');
            });
        }
    }
};
