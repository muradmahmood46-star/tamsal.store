<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreUnblockRequest extends Model
{
    protected $table = 'store_unblock_requests';

    protected $fillable = [
        'user_id',
        'seller_id',
        'store_name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'message',
        'fine_amount',
        'fine_status',
        'fine_imposed_at',
        'admin_reply',
        'admin_replied_at',
        'admin_id',
        'status',
        'is_seen',
        'admin_seen_at',
        'unblocked_at',
    ];

    protected $casts = [
        'fine_amount' => 'decimal:2',
        'fine_imposed_at' => 'datetime',
        'admin_replied_at' => 'datetime',
        'admin_seen_at' => 'datetime',
        'unblocked_at' => 'datetime',
        'is_seen' => 'integer',
    ];

    public function finePayments()
    {
        return $this->hasMany('App\Models\FinePayment', 'store_unblock_request_id');
    }

    public function latestFinePayment()
    {
        return $this->hasOne('App\Models\FinePayment', 'store_unblock_request_id')->latestOfMany();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id')->withDefault();
    }

    public function seller()
    {
        return $this->belongsTo('App\Models\Seller', 'seller_id')->withDefault();
    }

    public function admin()
    {
        return $this->belongsTo('App\Models\Admin', 'admin_id')->withDefault();
    }

    public function getStoreUrlAttribute()
    {
        if ($this->user_id) {
            return route('front.catalog') . '?vendor=' . $this->user_id;
        }
        return route('front.catalog') . '?vendor=admin';
    }

    public function getFullNameAttribute()
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }
}
