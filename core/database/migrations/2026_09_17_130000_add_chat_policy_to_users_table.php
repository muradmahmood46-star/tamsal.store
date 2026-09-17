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
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'chat_blocked')) {
                    $table->tinyInteger('chat_blocked')->default(0)->after('is_seller_blocked');
                }
                if (!Schema::hasColumn('users', 'chat_warnings_count')) {
                    $table->integer('chat_warnings_count')->default(0)->after('chat_blocked');
                }
                if (!Schema::hasColumn('users', 'chat_blocked_reason')) {
                    $table->string('chat_blocked_reason')->nullable()->after('chat_warnings_count');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'chat_blocked')) {
                    $table->dropColumn('chat_blocked');
                }
                if (Schema::hasColumn('users', 'chat_warnings_count')) {
                    $table->dropColumn('chat_warnings_count');
                }
                if (Schema::hasColumn('users', 'chat_blocked_reason')) {
                    $table->dropColumn('chat_blocked_reason');
                }
            });
        }
    }
};
