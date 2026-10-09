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
        // 1. Add fields to settings table
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                if (!Schema::hasColumn('settings', 'vendor_plan_mode')) {
                    $table->string('vendor_plan_mode', 30)->default('commission');
                }
                if (!Schema::hasColumn('settings', 'vendor_free_days')) {
                    $table->integer('vendor_free_days')->default(30);
                }
                if (!Schema::hasColumn('settings', 'vendor_plan_duration')) {
                    $table->integer('vendor_plan_duration')->default(30);
                }
                if (!Schema::hasColumn('settings', 'vendor_plan_charge')) {
                    $table->decimal('vendor_plan_charge', 12, 2)->default(1000.00);
                }
            });
        }

        // 2. Add fields to sellers table
        if (Schema::hasTable('sellers')) {
            Schema::table('sellers', function (Blueprint $table) {
                if (!Schema::hasColumn('sellers', 'plan_status')) {
                    $table->string('plan_status', 30)->default('free_time');
                }
                if (!Schema::hasColumn('sellers', 'plan_start_date')) {
                    $table->timestamp('plan_start_date')->nullable();
                }
                if (!Schema::hasColumn('sellers', 'plan_end_date')) {
                    $table->timestamp('plan_end_date')->nullable();
                }
                if (!Schema::hasColumn('sellers', 'plan_warned_at')) {
                    $table->timestamp('plan_warned_at')->nullable();
                }
            });
        }

        // 3. Add is_hidden_by_plan to items table
        if (Schema::hasTable('items')) {
            Schema::table('items', function (Blueprint $table) {
                if (!Schema::hasColumn('items', 'is_hidden_by_plan')) {
                    $table->tinyInteger('is_hidden_by_plan')->default(0)->index();
                }
            });
        }

        // 4. Add is_hidden_by_plan to deals table
        if (Schema::hasTable('deals')) {
            Schema::table('deals', function (Blueprint $table) {
                if (!Schema::hasColumn('deals', 'is_hidden_by_plan')) {
                    $table->tinyInteger('is_hidden_by_plan')->default(0)->index();
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
                $columns = ['vendor_plan_mode', 'vendor_free_days', 'vendor_plan_duration', 'vendor_plan_charge'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('settings', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('sellers')) {
            Schema::table('sellers', function (Blueprint $table) {
                $columns = ['plan_status', 'plan_start_date', 'plan_end_date', 'plan_warned_at'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('sellers', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('items') && Schema::hasColumn('items', 'is_hidden_by_plan')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('is_hidden_by_plan');
            });
        }

        if (Schema::hasTable('deals') && Schema::hasColumn('deals', 'is_hidden_by_plan')) {
            Schema::table('deals', function (Blueprint $table) {
                $table->dropColumn('is_hidden_by_plan');
            });
        }
    }
};
