<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class VendorPlanHistory extends Model
{
    protected $fillable = [
        'seller_id',
        'user_id',
        'plan_type',
        'plan_name',
        'duration_days',
        'days_added',
        'charge',
        'start_date',
        'end_date',
        'previous_end_date',
        'balance_after',
        'details',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'previous_end_date' => 'datetime',
        'charge' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    /**
     * Ensure table schema exists non-destructively.
     */
    public static function ensureTable(): void
    {
        try {
            if (!Schema::hasTable('vendor_plan_histories')) {
                Schema::create('vendor_plan_histories', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('seller_id')->index();
                    $table->unsignedBigInteger('user_id')->index();
                    $table->string('plan_type', 40)->default('manual_extension')->index();
                    $table->string('plan_name', 100)->default('Time-Based Store Plan');
                    $table->integer('duration_days')->default(30);
                    $table->integer('days_added')->default(30);
                    $table->decimal('charge', 12, 2)->default(0.00);
                    $table->timestamp('start_date')->nullable();
                    $table->timestamp('end_date')->nullable();
                    $table->timestamp('previous_end_date')->nullable();
                    $table->decimal('balance_after', 12, 2)->nullable();
                    $table->text('details')->nullable();
                    $table->string('status', 30)->default('active')->index();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Backfill / sync initial history for existing sellers so history is never empty.
     */
    public static function syncInitialHistory($seller): void
    {
        if (!$seller) return;

        self::ensureTable();

        $count = self::where('user_id', $seller->user_id)->count();
        if ($count > 0) {
            return;
        }

        // 1. Check existing plan_renewal transactions
        $transactions = VendorTransaction::where('user_id', $seller->user_id)
            ->where('type', 'plan_renewal')
            ->orderBy('id', 'asc')
            ->get();

        if ($transactions->count() > 0) {
            foreach ($transactions as $txn) {
                self::create([
                    'seller_id' => $seller->id,
                    'user_id' => $seller->user_id,
                    'plan_type' => 'plan_renewal',
                    'plan_name' => 'Time-Based Store Plan',
                    'duration_days' => 30,
                    'days_added' => 30,
                    'charge' => abs((float)$txn->amount),
                    'start_date' => $txn->created_at,
                    'end_date' => Carbon::parse($txn->created_at)->addDays(30),
                    'previous_end_date' => $txn->created_at,
                    'balance_after' => $txn->balance_after,
                    'details' => $txn->details ?: 'Plan Renewal',
                    'status' => 'completed',
                    'created_at' => $txn->created_at,
                    'updated_at' => $txn->created_at,
                ]);
            }
        }

        // 2. If seller has initial free_time or active plan, add initial record
        if ($seller->plan_start_date && $seller->plan_end_date) {
            $isFree = in_array($seller->plan_status, ['free_time', 'free_period']);
            $days = max(1, Carbon::parse($seller->plan_start_date)->diffInDays(Carbon::parse($seller->plan_end_date)));

            self::create([
                'seller_id' => $seller->id,
                'user_id' => $seller->user_id,
                'plan_type' => $isFree ? 'free_time' : 'active_plan',
                'plan_name' => $isFree ? 'Initial Free Time Period' : 'Time-Based Store Plan',
                'duration_days' => $days,
                'days_added' => $days,
                'charge' => 0.00,
                'start_date' => $seller->plan_start_date,
                'end_date' => $seller->plan_end_date,
                'previous_end_date' => null,
                'balance_after' => $seller->balance,
                'details' => $isFree ? 'Initial Free Time Subscription' : 'Store Plan Activation',
                'status' => $seller->isPlanExpired() ? 'expired' : 'active',
                'created_at' => $seller->plan_start_date,
                'updated_at' => $seller->plan_start_date,
            ]);
        }
    }
}
