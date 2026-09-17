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
}
