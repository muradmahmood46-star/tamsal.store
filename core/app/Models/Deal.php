<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Deal extends Model
{
    protected $fillable = [
        'vendor_id',
        'name',
        'slug',
        'description',
        'discount_type',
        'discount_value',
        'original_price',
        'discounted_price',
        'delivery_charge',
        'is_free_delivery',
        'duration_days',
        'start_date',
        'end_date',
        'status',
        'orders_count',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'original_price' => 'float',
        'discounted_price' => 'float',
        'discount_value' => 'float',
        'delivery_charge' => 'float',
        'is_free_delivery' => 'boolean',
        'orders_count' => 'integer',
        'status' => 'integer',
        'duration_days' => 'integer',
    ];

    public function dealItems()
    {
        return $this->hasMany(DealItem::class, 'deal_id');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'deal_items', 'deal_id', 'item_id')
                    ->withPivot('original_price', 'discounted_price')
                    ->withTimestamps();
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 0)
              ->orWhere('end_date', '<=', Carbon::now());
        });
    }

    public function isExpired()
    {
        return $this->status == 0 || ($this->end_date && $this->end_date->isPast());
    }

    public function getDiscountBadgeAttribute()
    {
        if ($this->discount_type === 'percent') {
            return round($this->discount_value) . '% OFF';
        } else {
            return '-' . \App\Helpers\PriceHelper::setCurrencyPrice($this->discount_value) . ' OFF';
        }
    }
}
