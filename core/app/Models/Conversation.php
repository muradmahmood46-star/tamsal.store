<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'item_id',
        'user_id',
        'vendor_id',
        'last_message',
        'last_message_at',
        'user_unread_count',
        'vendor_unread_count',
        'deleted_by_user',
        'deleted_by_vendor'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id')->withDefault();
    }

    public function vendor()
    {
        return $this->belongsTo('App\Models\User', 'vendor_id')->withDefault();
    }

    public function seller()
    {
        return $this->belongsTo('App\Models\Seller', 'vendor_id', 'user_id')->withDefault();
    }

    public function messages()
    {
        return $this->hasMany('App\Models\ChatMessage', 'conversation_id')->orderBy('id', 'asc');
    }

    public function getStoreNameAttribute()
    {
        if ($this->vendor_id && $this->vendor_id > 0) {
            if ($this->seller && $this->seller->shop_name) {
                return $this->seller->shop_name;
            }
            if ($this->vendor && $this->vendor->first_name) {
                return $this->vendor->first_name . '\'s Store';
            }
            return 'Vendor Store';
        }
        return 'ORIVO';
    }

    public function getBuyerNameAttribute()
    {
        if ($this->user_id == 0 || empty($this->user_id)) {
            if ($this->vendor && $this->vendor->first_name) {
                return trim($this->vendor->first_name . ' ' . ($this->vendor->last_name ?? ''));
            }
            if ($this->seller && $this->seller->shop_name) {
                return $this->seller->shop_name;
            }
            return 'Store Owner';
        }
        if ($this->user && $this->user->first_name) {
            return trim($this->user->first_name . ' ' . ($this->user->last_name ?? ''));
        }
        return 'Customer';
    }

    public function getStoreUrlAttribute()
    {
        if ($this->vendor_id && $this->vendor_id > 0) {
            return route('front.catalog') . '?vendor=' . $this->vendor_id;
        }
        return route('front.catalog') . '?vendor=admin';
    }
}
