<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class Notification extends Model
{
    protected $fillable = ['order_id', 'user_id', 'deposit_id', 'is_read', 'created_at', 'updated_at'];

    public static function ensureColumns()
    {
        try {
            if (Schema::hasTable('notifications')) {
                if (!Schema::hasColumn('notifications', 'deposit_id')) {
                    Schema::table('notifications', function (Blueprint $table) {
                        $table->unsignedBigInteger('deposit_id')->nullable()->after('user_id');
                    });
                }
            }
        } catch (\Throwable $e) {}
    }

    public static function syncPendingDepositNotifications()
    {
        self::ensureColumns();
        try {
            $pendingDeposits = DepositRequest::where('status', 'pending')->get();
            if ($pendingDeposits->isNotEmpty()) {
                $existingIds = self::whereIn('deposit_id', $pendingDeposits->pluck('id'))->pluck('deposit_id')->toArray();
                foreach ($pendingDeposits as $dep) {
                    if (!in_array($dep->id, $existingIds)) {
                        self::create([
                            'deposit_id' => $dep->id,
                            'user_id'    => null,
                            'order_id'   => null,
                            'is_read'    => 0,
                            'created_at' => $dep->created_at,
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {}
    }

    public function order()
    {
    	return $this->belongsTo('App\Models\Order')->withDefault();
    }

    public function user()
    {
    	return $this->belongsTo('App\Models\User')->withDefault();
    }

    public function deposit()
    {
    	return $this->belongsTo('App\Models\DepositRequest', 'deposit_id')->withDefault();
    }

    public static function countRegistration()
    {
        self::ensureColumns();
        return self::where('user_id','!=',null)->where('is_read','=',0)->count();
    }

    public static function countOrder()
    {
        self::ensureColumns();
        return self::where('order_id','!=',null)
            ->where('is_read','=',0)
            ->whereHas('order', function($q) {
                $q->whereNull('vendor_id')->orWhere('vendor_id', 0);
            })
            ->count();
    }

    public static function countDeposit()
    {
        self::ensureColumns();
        return self::where('deposit_id', '!=', null)
            ->where('is_read', '=', 0)
            ->count();
    }

    public static function totalUnread()
    {
        self::syncPendingDepositNotifications();
        return self::where('is_read', 0)->count();
    }
}
