<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['vendor_id', 'name', 'slug', 'photo', 'status', 'is_feature', 'meta_keywords', 'meta_descriptions', 'serial'];
    public $timestamps = false;

    public function items()
    {
        return $this->hasMany('App\Models\Item');
    }

    public function subcategory()
    {
        return $this->hasMany('App\Models\Subcategory');
    }

    public function vendor()
    {
        return $this->belongsTo('App\Models\User', 'vendor_id')->withDefault();
    }
}
