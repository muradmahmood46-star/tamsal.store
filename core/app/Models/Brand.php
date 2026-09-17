<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['vendor_id', 'name', 'slug', 'photo', 'status', 'is_popular'];
    public $timestamps = false;

    public function items()
    {
        return $this->hasMany('App\Models\Item');
    }

    public function vendor()
    {
        return $this->belongsTo('App\Models\User', 'vendor_id')->withDefault();
    }

}
