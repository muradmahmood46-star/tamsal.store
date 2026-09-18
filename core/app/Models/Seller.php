<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $fillable = [
        'user_id',
        'shop_name',
        'shop_address',
        'product_types',
        'courier_company',
        'shop_phone',
        'shop_email',
        'shop_logo',
        'shop_banner',
        'shop_details',
        'balance',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function depositRequests()
    {
        return $this->hasMany(DepositRequest::class, 'seller_id')->latest();
    }

    public function walletTransactions()
    {
        return $this->hasMany(VendorTransaction::class, 'seller_id')->latest();
    }

    public function products()
    {
        return $this->hasMany(Item::class, 'vendor_id', 'user_id')->latest();
    }

    public function logoUrl()
    {
        if (!empty($this->shop_logo)) {
            $storePath = public_path('storage/images/stores/' . $this->shop_logo);
            if (file_exists($storePath)) {
                return asset('storage/images/stores/' . $this->shop_logo);
            }
            $imgPath = public_path('storage/images/' . $this->shop_logo);
            if (file_exists($imgPath)) {
                return asset('storage/images/' . $this->shop_logo);
            }
            return asset('storage/images/stores/' . $this->shop_logo);
        }
        if ($this->user && !empty($this->user->photo)) {
            return $this->user->photoUrl();
        }
        return asset('storage/images/placeholder.png');
    }

    public function bannerUrl()
    {
        if (!empty($this->shop_banner)) {
            $storePath = public_path('storage/images/stores/' . $this->shop_banner);
            if (file_exists($storePath)) {
                return asset('storage/images/stores/' . $this->shop_banner);
            }
            return asset('storage/images/stores/' . $this->shop_banner);
        }
        return null;
    }
}
