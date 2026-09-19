<?php

namespace App\Models;

use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{

    protected $fillable = ['category_id','subcategory_id','childcategory_id','brand_id','name','slug','sku','tags','video','estimated_profit','sort_details','specification_name','specification_description','is_specification','details','photo','thumbnail','discount_price','previous_price','stock','item_variants','meta_keywords','meta_description','status','approval_status','reject_reason','is_hidden_by_block','vendor_id','is_type','tax_id','date','item_type','file','link','file_type','license_name','license_key','affiliate_link', "advance_payment_type", "advance_payment_amount", "is_free_delivery", "delivery_fee", "is_custom_rating", "custom_rating", "custom_rating_count", "is_returnable", "return_days"];

    public function getRatingAttribute()
    {
        $hasCustom = ($this->is_custom_rating == 1 && $this->custom_rating !== null && $this->custom_rating > 0);
        $customScore = $hasCustom ? (float) $this->custom_rating : 0.0;
        $customCount = $hasCustom ? (int) ($this->custom_rating_count ?: 1) : 0;

        if ($this->relationLoaded('reviews')) {
            $activeReviews = $this->reviews->where('status', 1);
            $realCount = $activeReviews->count();
            $realSum = (float) $activeReviews->sum('rating');
        } else {
            $realCount = (int) $this->reviews()->where('status', 1)->count();
            $realSum = (float) ($this->reviews()->where('status', 1)->sum('rating') ?: 0);
        }

        $totalCount = $customCount + $realCount;
        if ($totalCount <= 0) {
            return 0.0;
        }

        $totalSum = ($customScore * $customCount) + $realSum;
        $avg = round($totalSum / $totalCount, 1);
        return min(5.0, max(0.0, (float) $avg));
    }

    public function getRatingCountAttribute()
    {
        $hasCustom = ($this->is_custom_rating == 1 && $this->custom_rating !== null && $this->custom_rating > 0);
        $customCount = $hasCustom ? (int) ($this->custom_rating_count ?: 1) : 0;

        if ($this->relationLoaded('reviews')) {
            $realCount = $this->reviews->where('status', 1)->count();
        } else {
            $realCount = (int) $this->reviews()->where('status', 1)->count();
        }

        return $customCount + $realCount;
    }

    public function getCustomerRatingAttribute()
    {
        if ($this->relationLoaded('reviews')) {
            $customerReviews = $this->reviews->where('status', 1)->filter(function ($r) {
                return empty($r->is_admin_added) || $r->is_admin_added == 0;
            });
            $count = $customerReviews->count();
            if ($count <= 0) {
                return 0.0;
            }
            $sum = (float) $customerReviews->sum('rating');
            return min(5.0, max(0.0, round($sum / $count, 1)));
        }

        $reviewsQuery = $this->reviews()->where('status', 1)->where(function ($q) {
            $q->whereNull('is_admin_added')->orWhere('is_admin_added', 0);
        });
        $count = $reviewsQuery->count();
        if ($count <= 0) {
            return 0.0;
        }
        $avg = $reviewsQuery->avg('rating');
        return min(5.0, max(0.0, round((float)$avg, 1)));
    }

    public function getCustomerRatingCountAttribute()
    {
        if ($this->relationLoaded('reviews')) {
            $customerReviews = $this->reviews->where('status', 1)->filter(function ($r) {
                return empty($r->is_admin_added) || $r->is_admin_added == 0;
            });
            return $customerReviews->count();
        }

        return (int) $this->reviews()->where('status', 1)->where(function ($q) {
            $q->whereNull('is_admin_added')->orWhere('is_admin_added', 0);
        })->count();
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
        return ($setting && $setting->brand_name) ? $setting->brand_name : 'Official Store';
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

}
