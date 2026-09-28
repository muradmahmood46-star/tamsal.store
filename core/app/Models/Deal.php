<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Deal extends Model
{
    protected $fillable = [
        'vendor_id',
        'name',
        'sku',
        'slug',
        'photo',
        'description',
        'discount_type',
        'discount_value',
        'original_price',
        'discounted_price',
        'delivery_charge',
        'is_free_delivery',
        'advance_discount',
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
        'advance_discount' => 'float',
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
        return $query->where('status', 1)
                     ->where(function ($q) {
                         $q->whereNull('end_date')
                           ->orWhere('end_date', '>', Carbon::now());
                     });
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
        return $this->status == 0 || ($this->end_date && Carbon::parse($this->end_date)->isPast());
    }

    public function isActive()
    {
        return !$this->isExpired();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'deal_id');
    }

    public function getRatingAttribute()
    {
        $avg = $this->reviews()->where('status', 1)->avg('rating');
        if ($avg) {
            return (float) $avg;
        }
        if ($this->relationLoaded('dealItems') || $this->dealItems()->exists()) {
            $itemIds = $this->dealItems->pluck('item_id')->toArray();
            if (!empty($itemIds)) {
                $itemAvg = Review::whereIn('item_id', $itemIds)->where('status', 1)->avg('rating');
                if ($itemAvg) {
                    return (float) $itemAvg;
                }
            }
        }
        return 5.0;
    }

    public function getRatingCountAttribute()
    {
        $count = $this->reviews()->where('status', 1)->count();
        if ($count > 0) {
            return $count;
        }
        if ($this->relationLoaded('dealItems') || $this->dealItems()->exists()) {
            $itemIds = $this->dealItems->pluck('item_id')->toArray();
            if (!empty($itemIds)) {
                return Review::whereIn('item_id', $itemIds)->where('status', 1)->count();
            }
        }
        return 0;
    }

    public function getStoreNameAttribute()
    {
        if ($this->vendor_id && $this->vendor) {
            if ($this->vendor->seller && !empty($this->vendor->seller->shop_name)) {
                return $this->vendor->seller->shop_name;
            }
            if (!empty($this->vendor->shop_name)) {
                return $this->vendor->shop_name;
            }
            return trim($this->vendor->first_name . ' ' . $this->vendor->last_name . "'s Store");
        }
        $setting = \App\Models\Setting::find(1);
        return ($setting && !empty($setting->brand_name)) ? $setting->brand_name : (($setting && !empty($setting->title)) ? $setting->title : 'Official Store');
    }

    public function getDiscountBadgeAttribute()
    {
        if ($this->discount_type === 'percent') {
            return round($this->discount_value) . '% OFF';
        } else {
            return '-' . \App\Helpers\PriceHelper::setCurrencyPrice($this->discount_value) . ' OFF';
        }
    }

    /**
     * Auto-generate a unique 6-character alphanumeric SKU for bundles
     */
    public static function generateAutoSku()
    {
        $letters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $digits = '23456789';
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            // Generates 6-character alphanumeric SKU: BD + Letter + Digit + 2 Chars
            $sku = 'BD' . $letters[random_int(0, strlen($letters) - 1)] . $digits[random_int(0, strlen($digits) - 1)];
            for ($i = 0; $i < 2; $i++) {
                $sku .= $chars[random_int(0, strlen($chars) - 1)];
            }
        } while (self::where('sku', $sku)->exists() || \App\Models\Item::where('sku', $sku)->exists());

        return $sku;
    }
}
