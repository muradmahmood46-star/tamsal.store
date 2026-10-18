<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $table = 'user_addresses';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'province',
        'city',
        'area',
        'locality',
        'address1',
        'zip',
        'country',
        'is_default'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * Get single formatted full address string.
     */
    public function getFormattedAddressAttribute()
    {
        $parts = array_filter([
            $this->address1,
            $this->locality,
            $this->area,
            $this->city,
            $this->province,
            $this->country
        ]);
        return implode(', ', $parts);
    }

    /**
     * Get recipient full name.
     */
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
