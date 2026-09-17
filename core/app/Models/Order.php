<?php

namespace App\Models;
use DB;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'vendor_id',
        'user_info',
        'cart',
        'shipping',
        'discount',
        'payment_method',
        'txnid',
        'charge_id',
        'transaction_number',
        'checkout_ref',
        'order_status',
        'payment_status',
        'shipping_info',
        'billing_info',
        'currency_sign',
        'currency_value',
        'tax',
        'state_price',
        'state',
        'payment_screenshot',
        'account_name',
        'account_number',
        'bank_name',
        'is_locked',
        'commission_amount',
        'commission_status'
    ];

    public function user()
    {
    	return $this->belongsTo('App\Models\User')->withDefault();
    }

    public function seller()
    {
        return $this->belongsTo(\App\Models\Seller::class, 'vendor_id', 'user_id')->withDefault();
    }

    public function vendorUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'vendor_id')->withDefault();
    }

    public function getStoreNameAttribute()
    {
        if (empty($this->vendor_id) || $this->vendor_id == 0) {
            return __('ORIVO');
        }
        $seller = \App\Models\Seller::where('user_id', $this->vendor_id)->first();
        if ($seller && $seller->shop_name) {
            return $seller->shop_name;
        }
        $user = \App\Models\User::find($this->vendor_id);
        if ($user) {
            return $user->first_name . '\'s Store';
        }
        return __('Store #') . $this->vendor_id;
    }

    public function tracks()
    {
    	return $this->belongsTo('App\Models\TrackOrder','order_id')->withDefault();
    }

    public function tranaction()
    {
    	return $this->hasOne('App\Models\Transaction','order_id')->withDefault();
    }

    public function tracks_data()
    {
    	return $this->hasMany('App\Models\TrackOrder','order_id');
    }

    public function notificaton()
    {
    	return $this->hasMany('App\Models\Notification','order_id');
    }

}
