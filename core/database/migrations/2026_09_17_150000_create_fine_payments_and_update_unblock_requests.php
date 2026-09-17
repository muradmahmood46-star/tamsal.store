<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinePaymentsAndUpdateUnblockRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Add fine fields to store_unblock_requests table
        if (Schema::hasTable('store_unblock_requests')) {
            Schema::table('store_unblock_requests', function (Blueprint $table) {
                if (!Schema::hasColumn('store_unblock_requests', 'fine_amount')) {
                    $table->decimal('fine_amount', 12, 2)->nullable()->after('message');
                }
                if (!Schema::hasColumn('store_unblock_requests', 'fine_status')) {
                    $table->string('fine_status', 50)->default('none')->after('fine_amount'); // none, pending, submitted, paid, rejected
                }
                if (!Schema::hasColumn('store_unblock_requests', 'fine_imposed_at')) {
                    $table->timestamp('fine_imposed_at')->nullable()->after('fine_status');
                }
            });
        }

        // 2. Create fine_payments table
        if (!Schema::hasTable('fine_payments')) {
            Schema::create('fine_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_unblock_request_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('seller_id')->nullable()->index();
                $table->decimal('fine_amount', 12, 2);
                $table->string('payment_method', 255)->nullable();
                $table->string('bank_name', 255)->nullable();
                $table->string('account_name', 255)->nullable();
                $table->string('account_number', 255)->nullable();
                $table->string('txn_id', 255)->nullable()->index();
                $table->string('screenshot', 255)->nullable();
                $table->string('status', 50)->default('pending')->index(); // pending, approved, rejected
                $table->text('admin_note')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();
            });
        }

        // 3. Add fine_payment_id to vendor_transactions table
        if (Schema::hasTable('vendor_transactions')) {
            Schema::table('vendor_transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('vendor_transactions', 'fine_payment_id')) {
                    $table->unsignedBigInteger('fine_payment_id')->nullable()->after('deposit_request_id')->index();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('store_unblock_requests')) {
            Schema::table('store_unblock_requests', function (Blueprint $table) {
                if (Schema::hasColumn('store_unblock_requests', 'fine_imposed_at')) {
                    $table->dropColumn('fine_imposed_at');
                }
                if (Schema::hasColumn('store_unblock_requests', 'fine_status')) {
                    $table->dropColumn('fine_status');
                }
                if (Schema::hasColumn('store_unblock_requests', 'fine_amount')) {
                    $table->dropColumn('fine_amount');
                }
            });
        }

        Schema::dropIfExists('fine_payments');

        if (Schema::hasTable('vendor_transactions')) {
            Schema::table('vendor_transactions', function (Blueprint $table) {
                if (Schema::hasColumn('vendor_transactions', 'fine_payment_id')) {
                    $table->dropColumn('fine_payment_id');
                }
            });
        }
    }
}
