<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreRequest extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'cnic',
        'shop_name',
        'shop_address',
        'id_card_front',
        'selfie_with_id',
        'store_documents',
        'is_free',
        'store_fee',
        'account_type',
        'account_name',
        'account_number',
        'transaction_id',
        'payment_screenshot',
        'status',
        'seller_status',
        'reject_reason'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public static function countPending()
    {
        return self::where('status', 'Pending')->count();
    }
}
