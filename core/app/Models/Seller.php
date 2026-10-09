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
        'status',
        'plan_status',
        'plan_start_date',
        'plan_end_date',
        'plan_warned_at',
    ];

    protected $casts = [
        'plan_start_date' => 'datetime',
        'plan_end_date' => 'datetime',
        'plan_warned_at' => 'datetime',
        'balance' => 'float',
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

            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('sellers') && \Illuminate\Support\Facades\Schema::hasColumn('sellers', 'store_code')) {
                    $existsInSellers = \App\Models\Seller::where('store_code', $code)->exists();
                    if ($existsInSellers) {
                        continue;
                    }
                }
            } catch (\Throwable $e) {}

            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('settings') && \Illuminate\Support\Facades\Schema::hasColumn('settings', 'admin_store_code')) {
                    $existsInSettings = \App\Models\Setting::where('admin_store_code', $code)->exists();
                    if ($existsInSettings) {
                        continue;
                    }
                }
            } catch (\Throwable $e) {}

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

        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('sellers', 'store_code')) {
                $code = self::generateUniqueStoreCode();
                $this->store_code = $code;
                $this->save();
                return $code;
            }
        } catch (\Throwable $e) {}

        return (string)($this->user_id ?: $this->id);
    }

    public function getStoreUrl(): string
    {
        return url('/c/' . $this->getStoreCode());
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

    public function unblockRequest()
    {
        return $this->hasOne(\App\Models\StoreUnblockRequest::class, 'user_id', 'user_id')->latestOfMany();
    }

    public function isTimeBasedMode(): bool
    {
        $setting = Setting::first();
        return $setting && ($setting->vendor_plan_mode === 'time_based');
    }

    public function isPlanExpired(): bool
    {
        if (!$this->isTimeBasedMode()) {
            return false;
        }

        if ($this->plan_status === 'expired') {
            return true;
        }

        if ($this->plan_end_date && \Carbon\Carbon::parse($this->plan_end_date)->isPast()) {
            return true;
        }

        return false;
    }

    public function getPlanDaysRemainingAttribute(): int
    {
        if (!$this->plan_end_date) {
            return 0;
        }

        $end = \Carbon\Carbon::parse($this->plan_end_date);
        if ($end->isPast()) {
            return 0;
        }

        $diffSecs = \Carbon\Carbon::now()->diffInSeconds($end, false);
        if ($diffSecs <= 0) {
            return 0;
        }

        return (int) ceil($diffSecs / 86400);
    }

    public function getPlanStatusLabelAttribute(): string
    {
        if ($this->isPlanExpired()) {
            return __('Plan Expired');
        }

        if ($this->plan_status === 'active_plan') {
            return __('Active Plan');
        }

        if (in_array($this->plan_status, ['free_time', 'free_period'])) {
            return __('Free Time');
        }

        return __('Active Plan');
    }

    /**
     * Check plan status and auto-renew if expired and balance covers plan charge.
     */
    public function checkAndRenewPlan(): array
    {
        $setting = Setting::first();
        if (!$setting || $setting->vendor_plan_mode !== 'time_based') {
            return ['status' => 'not_time_based'];
        }

        if ((int)$this->status !== 1) {
            return ['status' => 'inactive_seller'];
        }

        $now = \Carbon\Carbon::now();
        $planCharge = (float)($setting->vendor_plan_charge ?? 1000.00);
        $planDuration = (int)($setting->vendor_plan_duration ?? 30);
        if ($planDuration < 1) {
            $planDuration = 30;
        }

        // Check if plan has ended or was not initialized
        $isDue = false;
        if (!$this->plan_end_date) {
            $isDue = true;
        } elseif (\Carbon\Carbon::parse($this->plan_end_date)->lte($now)) {
            $isDue = true;
        }

        if (!$isDue) {
            return ['status' => $this->plan_status ?: 'active_plan', 'days_remaining' => $this->plan_days_remaining];
        }

        // Due for renewal: Check wallet balance
        if ((float)$this->balance >= $planCharge && $planCharge >= 0) {
            $this->balance = (float)$this->balance - $planCharge;
            $this->plan_status = 'active_plan';
            $this->plan_start_date = $now;
            $this->plan_end_date = $now->copy()->addDays($planDuration);
            $this->plan_warned_at = null;
            $this->save();

            // Restore products & deals hidden by plan expiry
            Item::where('vendor_id', $this->user_id)
                ->where('is_hidden_by_plan', 1)
                ->update([
                    'status' => 1,
                    'is_hidden_by_plan' => 0
                ]);

            Deal::where('vendor_id', $this->user_id)
                ->where('is_hidden_by_plan', 1)
                ->update([
                    'status' => 1,
                    'is_hidden_by_plan' => 0
                ]);

            // Log Transaction
            $curr = \App\Helpers\PriceHelper::adminCurrency();
            VendorTransaction::create([
                'seller_id' => $this->id,
                'user_id' => $this->user_id,
                'type' => 'plan_renewal',
                'amount' => -$planCharge,
                'balance_after' => $this->balance,
                'details' => __('Plan renewed: :curr :amount for :days days', [
                    'curr' => $curr,
                    'amount' => number_format($planCharge, 2),
                    'days' => $planDuration
                ]),
                'status' => 'completed'
            ]);

            \App\Models\VendorNotification::log(
                $this->user_id,
                'plan_renewed',
                __('Store Plan Auto-Renewed!'),
                __('Your store plan has been auto-renewed for :days days until :date. :curr :amount was deducted from your store wallet.', [
                    'days' => $planDuration,
                    'date' => $this->plan_end_date->format('d M Y'),
                    'curr' => $curr,
                    'amount' => number_format($planCharge, 2)
                ]),
                route('seller.dashboard'),
                'fas fa-check-circle',
                'success'
            );

            return ['status' => 'renewed', 'plan_end_date' => $this->plan_end_date];
        } else {
            // Insufficient balance -> Mark Expired
            $wasExpired = ($this->plan_status === 'expired');
            $this->plan_status = 'expired';
            $this->save();

            // Hide active products & deals without deleting
            Item::where('vendor_id', $this->user_id)
                ->where('status', 1)
                ->where(function($q) {
                    $q->whereNull('is_hidden_by_block')->orWhere('is_hidden_by_block', 0);
                })
                ->update([
                    'status' => 0,
                    'is_hidden_by_plan' => 1
                ]);

            Deal::where('vendor_id', $this->user_id)
                ->where('status', 1)
                ->update([
                    'status' => 0,
                    'is_hidden_by_plan' => 1
                ]);

            if (!$wasExpired) {
                $curr = \App\Helpers\PriceHelper::adminCurrency();
                \App\Models\VendorNotification::log(
                    $this->user_id,
                    'plan_expired',
                    __('Store Plan Expired'),
                    __('Your plan has expired. Please deposit at least :curr :amount to continue managing your store.', [
                        'curr' => $curr,
                        'amount' => number_format($planCharge, 2)
                    ]),
                    route('seller.wallet.index'),
                    'fas fa-exclamation-triangle',
                    'danger'
                );
            }

            return ['status' => 'expired'];
        }
    }

    /**
     * Reactivate store plan after deposit request approval.
     */
    public function reactivatePlanAfterDeposit(): bool
    {
        $setting = Setting::first();
        if (!$setting || $setting->vendor_plan_mode !== 'time_based') {
            return false;
        }

        $planCharge = (float)($setting->vendor_plan_charge ?? 1000.00);
        $planDuration = (int)($setting->vendor_plan_duration ?? 30);
        if ($planDuration < 1) {
            $planDuration = 30;
        }

        $isExpiredOrEnding = ($this->plan_status === 'expired') || (!$this->plan_end_date) || \Carbon\Carbon::parse($this->plan_end_date)->lte(\Carbon\Carbon::now());

        if ($isExpiredOrEnding && (float)$this->balance >= $planCharge && $planCharge >= 0) {
            $now = \Carbon\Carbon::now();
            $this->balance = (float)$this->balance - $planCharge;
            $this->plan_status = 'active_plan';
            $this->plan_start_date = $now;
            $this->plan_end_date = $now->copy()->addDays($planDuration);
            $this->plan_warned_at = null;
            $this->save();

            // Restore hidden products & deals
            Item::where('vendor_id', $this->user_id)
                ->where('is_hidden_by_plan', 1)
                ->update([
                    'status' => 1,
                    'is_hidden_by_plan' => 0
                ]);

            Deal::where('vendor_id', $this->user_id)
                ->where('is_hidden_by_plan', 1)
                ->update([
                    'status' => 1,
                    'is_hidden_by_plan' => 0
                ]);

            $curr = \App\Helpers\PriceHelper::adminCurrency();
            VendorTransaction::create([
                'seller_id' => $this->id,
                'user_id' => $this->user_id,
                'type' => 'plan_renewal',
                'amount' => -$planCharge,
                'balance_after' => $this->balance,
                'details' => __('Plan renewed: :curr :amount for :days days', [
                    'curr' => $curr,
                    'amount' => number_format($planCharge, 2),
                    'days' => $planDuration
                ]),
                'status' => 'completed'
            ]);

            \App\Models\VendorNotification::log(
                $this->user_id,
                'plan_reactivated',
                __('Store Plan Re-Activated!'),
                __('Your store plan has been successfully re-activated for :days days until :date. :curr :amount was deducted from your wallet.', [
                    'days' => $planDuration,
                    'date' => $this->plan_end_date->format('d M Y'),
                    'curr' => $curr,
                    'amount' => number_format($planCharge, 2)
                ]),
                route('seller.dashboard'),
                'fas fa-check-circle',
                'success'
            );

            return true;
        }

        return false;
    }
}
