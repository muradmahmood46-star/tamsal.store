<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositRequest extends Model
{
    protected $fillable = [
        'seller_id',
        'user_id',
        'payment_method',
        'bank_name',
        'account_name',
        'account_number',
        'amount',
        'txn_id',
        'screenshot',
        'status',
        'admin_note'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id')->withDefault();
    }
}
