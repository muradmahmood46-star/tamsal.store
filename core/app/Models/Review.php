<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['user_id','item_id','customer_name','is_admin_added','review','rating','status','subject'];

    public function user()
    {
    	return $this->belongsTo('App\Models\User')->withDefault();
    }

    public function item()
    {
    	return $this->belongsTo('App\Models\Item')->withDefault();
    }

    public function getReviewerNameAttribute()
    {
        if (!empty($this->customer_name)) {
            return $this->customer_name;
        }
        if ($this->user && !empty($this->user->first_name)) {
            return trim($this->user->first_name . ' ' . $this->user->last_name);
        }
        return __('Verified Buyer');
    }

    public static function ratings($item_id){
        $item = Item::find($item_id);
        if ($item && $item->is_custom_rating == 1 && $item->custom_rating !== null && $item->custom_rating > 0) {
            return number_format((float)$item->custom_rating, 1, '.', '') * 20;
        }
        $stars = Review::whereStatus(1)->whereItemId($item_id)->avg('rating');
        $ratings = number_format((float)$stars, 1, '.', '') * 20;
        return $ratings;
    }


}
