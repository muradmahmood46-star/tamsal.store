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
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                if (!Schema::hasColumn('settings', 'is_delivery_calc_enabled')) {
                    $table->tinyInteger('is_delivery_calc_enabled')->default(1);
                }
                if (!Schema::hasColumn('settings', 'delivery_second_item_divisor')) {
                    $table->decimal('delivery_second_item_divisor', 10, 4)->default(2.0000);
                }
                if (!Schema::hasColumn('settings', 'delivery_subsequent_item_divisor')) {
                    $table->decimal('delivery_subsequent_item_divisor', 10, 4)->default(3.0000);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                $columns = [
                    'is_delivery_calc_enabled',
                    'delivery_second_item_divisor',
                    'delivery_subsequent_item_divisor',
                ];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('settings', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
