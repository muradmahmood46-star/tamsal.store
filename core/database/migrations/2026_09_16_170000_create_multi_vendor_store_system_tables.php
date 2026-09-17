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
        // 1. store_requests table
        if (!Schema::hasTable('store_requests')) {
            Schema::create('store_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('cnic')->nullable();
                $table->string('shop_name')->nullable();
                $table->text('shop_address')->nullable();
                $table->string('id_card_front')->nullable();
                $table->string('selfie_with_id')->nullable();
                $table->string('store_documents')->nullable();
                $table->tinyInteger('is_free')->default(0);
                $table->decimal('store_fee', 10, 2)->default(0);
                $table->string('account_type')->nullable();
                $table->string('account_name')->nullable();
                $table->string('account_number')->nullable();
                $table->string('transaction_id')->nullable();
                $table->string('payment_screenshot')->nullable();
                $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
                $table->text('reject_reason')->nullable();
                $table->timestamps();
            });
        }

        // 2. receiving_accounts table
        if (!Schema::hasTable('receiving_accounts')) {
            Schema::create('receiving_accounts', function (Blueprint $table) {
                $table->id();
                $table->string('payment_method')->nullable();
                $table->string('account_name')->nullable();
                $table->string('account_number')->nullable();
                $table->text('note')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        // 3. sellers table
        if (!Schema::hasTable('sellers')) {
            Schema::create('sellers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('shop_name')->nullable();
                $table->text('shop_address')->nullable();
                $table->string('shop_phone')->nullable();
                $table->string('shop_email')->nullable();
                $table->string('shop_logo')->nullable();
                $table->string('shop_banner')->nullable();
                $table->text('shop_details')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        // 4. Add store settings columns to settings table
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                if (!Schema::hasColumn('settings', 'store_opening_fee')) {
                    $table->decimal('store_opening_fee', 10, 2)->default(0)->after('is_mail_verify');
                }
                if (!Schema::hasColumn('settings', 'is_store_opening_free')) {
                    $table->tinyInteger('is_store_opening_free')->default(1)->after('store_opening_fee');
                }
            });
        }

        // 5. Add is_seller column to users table
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'is_seller')) {
                    $table->tinyInteger('is_seller')->default(0)->after('email_verify');
                }
            });
        }

        // 6. Add vendor_id column to items table if not present
        if (Schema::hasTable('items')) {
            Schema::table('items', function (Blueprint $table) {
                if (!Schema::hasColumn('items', 'vendor_id')) {
                    $table->unsignedBigInteger('vendor_id')->default(0)->after('status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_requests');
        Schema::dropIfExists('receiving_accounts');
        Schema::dropIfExists('sellers');
    }
};
