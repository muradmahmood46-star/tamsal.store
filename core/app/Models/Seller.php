<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $fillable = [
        'user_id',
        'store_code',
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

    public static function generateUniqueStoreCode(): string
    {
        $chars = '23456789abcdefghjkmnpqrstuvwxyz';
        $length = 5;
        $maxAttempts = 100;

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }

            if (!preg_match('/[a-z]/', $code) || !preg_match('/[0-9]/', $code)) {
                continue;
            }

            $existsInSellers = \App\Models\Seller::where('store_code', $code)->exists();
            if ($existsInSellers) {
                continue;
            }

            $existsInSettings = \App\Models\Setting::where('admin_store_code', $code)->exists();
            if ($existsInSettings) {
                continue;
            }

            return $code;
        }

        return substr(md5(uniqid((string)mt_rand(), true)), 0, 5);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($seller) {
            if (empty($seller->store_code)) {
                $seller->store_code = self::generateUniqueStoreCode();
            }
        });
    }

    public function getStoreCode(): string
    {
        if (!empty($this->store_code)) {
            return (string)$this->store_code;
        }

        $code = self::generateUniqueStoreCode();
        $this->store_code = $code;
        $this->saveQuietly();
        return $code;
    }

    public function getStoreUrl(): string
    {
        return route('front.catalog', ['vendor' => $this->getStoreCode()]);
    }

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
