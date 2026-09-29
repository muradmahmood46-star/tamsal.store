<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ItemPromotion extends Model
{
    protected $fillable = [
        'seller_id',
        'user_id',
        'item_type',
        'item_id',
        'tag_name',
        'tag_id',
        'days',
        'price',
        'starts_at',
        'expires_at',
        'status'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id')->withDefault();
    }

    public function product()
    {
        return $this->belongsTo(Item::class, 'item_id')->withDefault();
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class, 'item_id')->withDefault();
    }

    public function getItemTitleAttribute()
    {
        if ($this->item_type === 'bundle' || $this->item_type === 'deal') {
            return $this->deal->name ?? ('Bundle #' . $this->item_id);
        }
        return $this->product->name ?? ('Product #' . $this->item_id);
    }

    public function getItemPhotoAttribute()
    {
        if ($this->item_type === 'bundle' || $this->item_type === 'deal') {
            if ($this->deal && $this->deal->photo) {
                return \Illuminate\Support\Str::startsWith($this->deal->photo, 'images/')
                    ? url('/core/public/storage/' . $this->deal->photo)
                    : url('/core/public/storage/images/' . $this->deal->photo);
            }
            return asset('storage/images/placeholder.png');
        }
        if ($this->product && ($this->product->photo || $this->product->thumbnail)) {
            $thumb = $this->product->photo ?: $this->product->thumbnail;
            return \Illuminate\Support\Str::startsWith($thumb, 'images/')
                ? url('/core/public/storage/' . $thumb)
                : url('/core/public/storage/images/' . $thumb);
        }
        return asset('storage/images/placeholder.png');
    }

    public function isCurrentlyActive()
    {
        return $this->status === 'active' && $this->expires_at && $this->expires_at->isFuture();
    }

    public static function ensureTable()
    {
        try {
            PromotionTag::ensureTable();
            PromotionPlan::ensureTable();

            if (!Schema::hasTable('item_promotions')) {
                Schema::create('item_promotions', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('seller_id')->index();
                    $table->unsignedBigInteger('user_id')->index();
                    $table->string('item_type')->default('product'); // 'product' or 'bundle'
                    $table->unsignedBigInteger('item_id')->index();
                    $table->string('tag_name');
                    $table->unsignedBigInteger('tag_id')->nullable();
                    $table->integer('days');
                    $table->decimal('price', 10, 2);
                    $table->dateTime('starts_at');
                    $table->dateTime('expires_at');
                    $table->string('status')->default('active'); // 'active', 'expired'
                    $table->timestamps();
                });
            }

            // Ensure columns in items table
            if (Schema::hasTable('items')) {
                Schema::table('items', function (Blueprint $table) {
                    if (!Schema::hasColumn('items', 'is_promoted')) {
                        $table->tinyInteger('is_promoted')->default(0);
                    }
                    if (!Schema::hasColumn('items', 'promotion_tag')) {
                        $table->string('promotion_tag')->nullable();
                    }
                    if (!Schema::hasColumn('items', 'promotion_tag_id')) {
                        $table->unsignedBigInteger('promotion_tag_id')->nullable();
                    }
                    if (!Schema::hasColumn('items', 'promotion_days')) {
                        $table->integer('promotion_days')->nullable();
                    }
                    if (!Schema::hasColumn('items', 'promotion_price')) {
                        $table->decimal('promotion_price', 10, 2)->nullable();
                    }
                    if (!Schema::hasColumn('items', 'promotion_starts_at')) {
                        $table->dateTime('promotion_starts_at')->nullable();
                    }
                    if (!Schema::hasColumn('items', 'promotion_expires_at')) {
                        $table->dateTime('promotion_expires_at')->nullable();
                    }
                });
            }

            // Ensure columns in deals table
            if (Schema::hasTable('deals')) {
                Schema::table('deals', function (Blueprint $table) {
                    if (!Schema::hasColumn('deals', 'is_promoted')) {
                        $table->tinyInteger('is_promoted')->default(0);
                    }
                    if (!Schema::hasColumn('deals', 'promotion_tag')) {
                        $table->string('promotion_tag')->nullable();
                    }
                    if (!Schema::hasColumn('deals', 'promotion_tag_id')) {
                        $table->unsignedBigInteger('promotion_tag_id')->nullable();
                    }
                    if (!Schema::hasColumn('deals', 'promotion_days')) {
                        $table->integer('promotion_days')->nullable();
                    }
                    if (!Schema::hasColumn('deals', 'promotion_price')) {
                        $table->decimal('promotion_price', 10, 2)->nullable();
                    }
                    if (!Schema::hasColumn('deals', 'promotion_starts_at')) {
                        $table->dateTime('promotion_starts_at')->nullable();
                    }
                    if (!Schema::hasColumn('deals', 'promotion_expires_at')) {
                        $table->dateTime('promotion_expires_at')->nullable();
                    }
                });
            }
        } catch (\Throwable $e) {
            // Safety fallback
        }
    }
}
