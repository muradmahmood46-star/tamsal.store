<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorTransaction extends Model
{
    protected $fillable = [
        'seller_id',
        'user_id',
        'type',
        'amount',
        'balance_after',
        'order_id',
        'deposit_request_id',
        'fine_payment_id',
        'details',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id')->withDefault();
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id')->withDefault();
    }

    public function depositRequest()
    {
        return $this->belongsTo(DepositRequest::class, 'deposit_request_id')->withDefault();
    }

    public function finePayment()
    {
        return $this->belongsTo(FinePayment::class, 'fine_payment_id')->withDefault();
    }
}
