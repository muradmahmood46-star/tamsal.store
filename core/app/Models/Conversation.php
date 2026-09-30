<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'item_id',
        'deal_id',
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

    public function deal()
    {
        return $this->belongsTo('App\Models\Deal', 'deal_id')->withDefault();
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
        $setting = \App\Models\Setting::find(1);
        return ($setting && !empty($setting->brand_name)) ? $setting->brand_name : (($setting && !empty($setting->title)) ? $setting->title : 'Official Store');
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
            if ($this->seller && $this->seller->id) {
                return $this->seller->getStoreUrl();
            }
            $seller = \App\Models\Seller::where('user_id', $this->vendor_id)->first();
            if ($seller) {
                return $seller->getStoreUrl();
            }
            return url('/c/' . $this->vendor_id);
        }
        $setting = \App\Models\Setting::first();
        return $setting ? $setting->getAdminStoreUrl() : url('/c/admin');
    }
}
