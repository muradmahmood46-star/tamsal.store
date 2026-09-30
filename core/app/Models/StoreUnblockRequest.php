<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreUnblockRequest extends Model
{
    protected $table = 'store_unblock_requests';

    protected $fillable = [
        'user_id',
        'seller_id',
        'store_name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'message',
        'fine_amount',
        'fine_status',
        'fine_imposed_at',
        'admin_reply',
        'admin_replied_at',
        'admin_id',
        'status',
        'is_seen',
        'admin_seen_at',
        'unblocked_at',
    ];

    protected $casts = [
        'fine_amount' => 'decimal:2',
        'fine_imposed_at' => 'datetime',
        'admin_replied_at' => 'datetime',
        'admin_seen_at' => 'datetime',
        'unblocked_at' => 'datetime',
        'is_seen' => 'integer',
    ];

    public function finePayments()
    {
        return $this->hasMany('App\Models\FinePayment', 'store_unblock_request_id');
    }

    public function latestFinePayment()
    {
        return $this->hasOne('App\Models\FinePayment', 'store_unblock_request_id')->latestOfMany();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id')->withDefault();
    }

    public function seller()
    {
        return $this->belongsTo('App\Models\Seller', 'seller_id')->withDefault();
    }

    public function admin()
    {
        return $this->belongsTo('App\Models\Admin', 'admin_id')->withDefault();
    }

    public function getStoreUrlAttribute()
    {
        if ($this->user_id) {
            if ($this->seller && $this->seller->id) {
                return $this->seller->getStoreUrl();
            }
            $seller = \App\Models\Seller::where('user_id', $this->user_id)->first();
            if ($seller) {
                return $seller->getStoreUrl();
            }
            return url('/c/' . $this->user_id);
        }
        $setting = \App\Models\Setting::first();
        return $setting ? $setting->getAdminStoreUrl() : url('/c/admin');
    }

    public function getFullNameAttribute()
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public static function ensureTable()
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('store_unblock_requests')) {
                \Illuminate\Support\Facades\Schema::create('store_unblock_requests', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id')->nullable()->index();
                    $table->unsignedBigInteger('seller_id')->nullable()->index();
                    $table->string('store_name')->nullable();
                    $table->string('first_name')->nullable();
                    $table->string('last_name')->nullable();
                    $table->string('email')->nullable();
                    $table->string('phone')->nullable();
                    $table->text('message')->nullable();
                    $table->decimal('fine_amount', 12, 2)->nullable();
                    $table->string('fine_status', 50)->default('none');
                    $table->timestamp('fine_imposed_at')->nullable();
                    $table->text('admin_reply')->nullable();
                    $table->timestamp('admin_replied_at')->nullable();
                    $table->unsignedBigInteger('admin_id')->nullable();
                    $table->string('status', 50)->default('Pending')->index();
                    $table->tinyInteger('is_seen')->default(0)->index();
                    $table->timestamp('admin_seen_at')->nullable();
                    $table->timestamp('unblocked_at')->nullable();
                    $table->timestamps();
                });
            } else {
                if (!\Illuminate\Support\Facades\Schema::hasColumn('store_unblock_requests', 'fine_amount')) {
                    try {
                        \Illuminate\Support\Facades\Schema::table('store_unblock_requests', function ($table) {
                            $table->decimal('fine_amount', 12, 2)->nullable();
                        });
                    } catch (\Throwable $e) {}
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('store_unblock_requests', 'fine_status')) {
                    try {
                        \Illuminate\Support\Facades\Schema::table('store_unblock_requests', function ($table) {
                            $table->string('fine_status', 50)->default('none');
                        });
                    } catch (\Throwable $e) {}
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('store_unblock_requests', 'fine_imposed_at')) {
                    try {
                        \Illuminate\Support\Facades\Schema::table('store_unblock_requests', function ($table) {
                            $table->timestamp('fine_imposed_at')->nullable();
                        });
                    } catch (\Throwable $e) {}
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('store_unblock_requests', 'is_seen')) {
                    try {
                        \Illuminate\Support\Facades\Schema::table('store_unblock_requests', function ($table) {
                            $table->tinyInteger('is_seen')->default(0)->index();
                        });
                    } catch (\Throwable $e) {}
                }
            }
        } catch (\Throwable $e) {}
    }
}
