<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinePayment extends Model
{
    protected $table = 'fine_payments';

    protected $fillable = [
        'store_unblock_request_id',
        'user_id',
        'seller_id',
        'fine_amount',
        'payment_method',
        'bank_name',
        'account_name',
        'account_number',
        'txn_id',
        'screenshot',
        'status', // pending, approved, rejected
        'admin_note',
        'approved_at',
    ];

    protected $casts = [
        'fine_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id')->withDefault();
    }

    public function unblockRequest()
    {
        return $this->belongsTo(StoreUnblockRequest::class, 'store_unblock_request_id')->withDefault();
    }

    public function vendorTransaction()
    {
        return $this->hasOne(VendorTransaction::class, 'fine_payment_id');
    }

    public static function ensureTable()
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('fine_payments')) {
                \Illuminate\Support\Facades\Schema::create('fine_payments', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('store_unblock_request_id')->nullable()->index();
                    $table->unsignedBigInteger('user_id')->index();
                    $table->unsignedBigInteger('seller_id')->nullable()->index();
                    $table->decimal('fine_amount', 12, 2)->default(0.00);
                    $table->string('payment_method', 255)->nullable();
                    $table->string('bank_name', 255)->nullable();
                    $table->string('account_name', 255)->nullable();
                    $table->string('account_number', 255)->nullable();
                    $table->string('txn_id', 255)->nullable()->index();
                    $table->string('screenshot', 255)->nullable();
                    $table->string('status', 50)->default('pending')->index();
                    $table->text('admin_note')->nullable();
                    $table->timestamp('approved_at')->nullable();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {}
    }
}
