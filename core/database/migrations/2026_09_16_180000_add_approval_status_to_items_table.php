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
        if (Schema::hasTable('items')) {
            Schema::table('items', function (Blueprint $table) {
                if (!Schema::hasColumn('items', 'approval_status')) {
                    $table->enum('approval_status', ['Pending', 'Approved', 'Rejected'])->default('Approved')->after('status');
                }
                if (!Schema::hasColumn('items', 'reject_reason')) {
                    $table->text('reject_reason')->nullable()->after('approval_status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('items')) {
            Schema::table('items', function (Blueprint $table) {
                if (Schema::hasColumn('items', 'approval_status')) {
                    $table->dropColumn('approval_status');
                }
                if (Schema::hasColumn('items', 'reject_reason')) {
                    $table->dropColumn('reject_reason');
                }
            });
        }
    }
};
