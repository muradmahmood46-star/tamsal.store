<?php

namespace App\Models;

use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{

    protected $fillable = ['category_id','subcategory_id','childcategory_id','brand_id','name','slug','sku','tags','video','estimated_profit','is_cod','product_from','contact_number','supplier_url','sort_details','specification_name','specification_description','is_specification','details','photo','thumbnail','discount_price','previous_price','stock','item_variants','meta_keywords','meta_description','status','approval_status','reject_reason','is_hidden_by_block','is_hidden_by_plan','vendor_id','is_type','tax_id','date','item_type','file','link','file_type','license_name','license_key','affiliate_link', "advance_payment_type", "advance_payment_amount", "is_free_delivery", "delivery_fee", "is_custom_rating", "custom_rating", "custom_rating_count", "is_returnable", "return_days", "is_promoted", "promotion_tag", "promotion_tag_id", "promotion_days", "promotion_price", "promotion_starts_at", "promotion_expires_at"];

    public function isCodAvailable(): bool
    {
        if (!empty($this->vendor_id) && (int)$this->vendor_id > 0) {
            return true;
        }
        return (!empty($this->is_cod) && (int)$this->is_cod == 1);
    }

    public function getRatingAttribute()
    {
        // 1. If real customer reviews exist, calculate directly from real customer ratings
        if ($this->relationLoaded('reviews')) {
            $realReviews = $this->reviews->where('status', 1)->filter(function ($r) {
                return empty($r->is_admin_added) || $r->is_admin_added == 0;
            });
            $realCount = $realReviews->count();
            if ($realCount > 0) {
                $realSum = (float) $realReviews->sum('rating');
                return min(5.0, max(0.0, (float) round($realSum / $realCount, 1)));
            }
        } else {
            $realReviewsQuery = $this->reviews()->where('status', 1)->where(function ($q) {
                $q->whereNull('is_admin_added')->orWhere('is_admin_added', 0);
            });
            $realCount = $realReviewsQuery->count();
            if ($realCount > 0) {
                $avg = $realReviewsQuery->avg('rating');
                return min(5.0, max(0.0, (float) round($avg, 1)));
            }
        }

        // 2. If no real customer reviews yet, fallback to initial custom / demo rating set at creation
        if ($this->is_custom_rating == 1 && $this->custom_rating !== null && $this->custom_rating > 0) {
            return min(5.0, max(0.0, (float) $this->custom_rating));
        }

        // 3. Fallback to any active demo/admin reviews average
        if ($this->relationLoaded('reviews')) {
            $allReviews = $this->reviews->where('status', 1);
            if ($allReviews->count() > 0) {
                return min(5.0, max(0.0, (float) round($allReviews->sum('rating') / $allReviews->count(), 1)));
            }
        } else {
            $allReviewsQuery = $this->reviews()->where('status', 1);
            if ($allReviewsQuery->count() > 0) {
                return min(5.0, max(0.0, (float) round($allReviewsQuery->avg('rating'), 1)));
            }
        }

        return 0.0;
    }

    public function getRatingCountAttribute()
    {
        // 1. If real customer reviews exist, return real customer count
        if ($this->relationLoaded('reviews')) {
            $realReviews = $this->reviews->where('status', 1)->filter(function ($r) {
                return empty($r->is_admin_added) || $r->is_admin_added == 0;
            });
            $realCount = $realReviews->count();
            if ($realCount > 0) {
                return $realCount;
            }
        } else {
            $realCount = $this->reviews()->where('status', 1)->where(function ($q) {
                $q->whereNull('is_admin_added')->orWhere('is_admin_added', 0);
            })->count();
            if ($realCount > 0) {
                return $realCount;
            }
        }

        // 2. If no real customer reviews yet, return initial custom rating count
        if ($this->is_custom_rating == 1 && $this->custom_rating !== null && $this->custom_rating > 0) {
            return (int) ($this->custom_rating_count ?: 1);
        }

        // 3. Fallback to any demo reviews count
        if ($this->relationLoaded('reviews')) {
            return $this->reviews->where('status', 1)->count();
        } else {
            return (int) $this->reviews()->where('status', 1)->count();
        }
    }

    public function getCustomerRatingAttribute()
    {
        return $this->rating;
    }

    public function getCustomerRatingCountAttribute()
    {
        return $this->rating_count;
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category')->withDefault();
    }

    public function subcategory()
    {
        return $this->belongsTo('App\Models\Subcategory')->withDefault();
    }

    public function childcategory()
    {
        return $this->belongsTo('App\Models\ChieldCategory')->withDefault();
    }

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand')->withDefault();
    }

    public function campaigns()
    {
        return $this->hasMany('App\Models\CampaignItem');
    }

    public function tax()
    {
        return $this->belongsTo('App\Models\Tax')->withDefault();
    }

    public function attributes()
    {
        return $this->hasMany('App\Models\Attribute');
    }

    public function galleries()
    {
        return $this->hasMany('App\Models\Gallery');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\Review');
    }

    public static function taxCalculate($item)
    {
        if($item->tax){
            $price = $item->discount_price;
            $percentage = $item->tax->value;
            $tax = ($price * $percentage) / 100;
            return $tax;
        }else{
            return 0;
        }
        
    }




    public function getWishlistItemId()
    {
        return Wishlist::whereItemId($this->id)->first()->id;
    }


    public function user()
    {
    	return $this->belongsTo('App\Models\User','vendor_id')->withDefault();
    }

    public function seller()
    {
        return $this->belongsTo('App\Models\Seller', 'vendor_id', 'user_id')->withDefault();
    }

    public function getStoreNameAttribute()
    {
        if ($this->vendor_id && $this->vendor_id > 0) {
            if ($this->seller && $this->seller->shop_name) {
                return $this->seller->shop_name;
            }
            if ($this->user && $this->user->first_name) {
                return $this->user->first_name . '\'s Store';
            }
            return 'Vendor Store';
        }
        $setting = \App\Models\Setting::find(1);
        return ($setting && !empty($setting->brand_name)) ? $setting->brand_name : (($setting && !empty($setting->title)) ? $setting->title : 'Official Store');
    }

    public function isPending()
    {
        return $this->approval_status === 'Pending';
    }

    public function isApproved()
    {
        return $this->approval_status === 'Approved';
    }

    public function isRejected()
    {
        return $this->approval_status === 'Rejected';
    }


    public function is_stock()
    {
        $item = $this;
        // license product stock check------------
        if($item->item_type == 'license'){
            if($item->license_key){
                $lisense_key = json_decode($item->license_key,true);
                if(count($lisense_key) > 0){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }

        // digital product stock check-------------

        if($item->item_type == 'digital'){
            return true;
        }
        if($item->item_type == 'affiliate'){
            return true;
        }

        // physical product stock check

        if($item->item_type == 'normal'){
            if($item->stock){
                if($item->stock != 0){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
          
        }
     
    }

    public function getProductSlugAttribute()
    {
        return !empty($this->sku) ? $this->sku : $this->slug;
    }

    public function productUrl()
    {
        return route('front.product', $this->product_slug);
    }

    public function isPromotionActive()
    {
        if ($this->is_promoted == 1 && !empty($this->promotion_tag)) {
            if (empty($this->promotion_expires_at)) {
                return true;
            }
            try {
                return \Carbon\Carbon::parse($this->promotion_expires_at)->isFuture();
            } catch (\Throwable $e) {
                return false;
            }
        }
        return false;
    }

    public function getActivePromotionTagAttribute()
    {
        return $this->isPromotionActive() ? $this->promotion_tag : null;
    }

    public function getStoreUrl(): string
    {
        if ($this->vendor_id && (int)$this->vendor_id > 0) {
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

    public function getStoreCode(): string
    {
        if ($this->vendor_id && (int)$this->vendor_id > 0) {
            if ($this->seller && $this->seller->id) {
                return $this->seller->getStoreCode();
            }
            $seller = \App\Models\Seller::where('user_id', $this->vendor_id)->first();
            if ($seller) {
                return $seller->getStoreCode();
            }
            return (string)$this->vendor_id;
        }
        $setting = \App\Models\Setting::first();
        return $setting ? $setting->getAdminStoreCode() : 'admin';
    }
}
