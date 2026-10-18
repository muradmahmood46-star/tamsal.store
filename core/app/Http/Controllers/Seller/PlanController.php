<?php

namespace App\Http\Controllers\Seller;

use App\Helpers\PriceHelper;
use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Item;
use App\Models\Seller;
use App\Models\Setting;
use App\Models\VendorNotification;
use App\Models\VendorPlanHistory;
use App\Models\VendorTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'seller']);
    }

    public function index()
    {
        $user = Auth::user();
        $seller = Seller::where('user_id', $user->id)->firstOrFail();
        $setting = Setting::first();

        VendorPlanHistory::ensureTable();
        VendorPlanHistory::syncInitialHistory($seller);

        $planCharge = (float)($setting->vendor_plan_charge ?? 1000.00);
        $planDuration = (int)($setting->vendor_plan_duration ?? 30);
        if ($planDuration < 1) {
            $planDuration = 30;
        }

        $isPlanExpired = $seller->isPlanExpired();
        $daysRemaining = $seller->plan_days_remaining;
        $planEndDate = $seller->plan_end_date ? Carbon::parse($seller->plan_end_date) : null;
        $isSufficient = (float)$seller->balance >= $planCharge;

        // Calculate projected end date and stacked days if extended now
        if ($planEndDate && $planEndDate->isFuture()) {
            $projectedEndDate = $planEndDate->copy()->addDays($planDuration);
            $projectedDays = $daysRemaining + $planDuration;
        } else {
            $projectedEndDate = Carbon::now()->addDays($planDuration);
            $projectedDays = $planDuration;
        }

        $histories = VendorPlanHistory::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('seller.plan.index', compact(
            'user',
            'seller',
            'setting',
            'planCharge',
            'planDuration',
            'isPlanExpired',
            'daysRemaining',
            'planEndDate',
            'isSufficient',
            'projectedEndDate',
            'projectedDays',
            'histories'
        ));
    }

    public function renew(Request $request)
    {
        $user = Auth::user();
        $seller = Seller::where('user_id', $user->id)->firstOrFail();
        $setting = Setting::first();

        $planCharge = (float)($setting->vendor_plan_charge ?? 1000.00);
        $planDuration = (int)($setting->vendor_plan_duration ?? 30);
        if ($planDuration < 1) {
            $planDuration = 30;
        }

        if ((float)$seller->balance < $planCharge) {
            $curr = PriceHelper::adminCurrency();
            $needed = number_format($planCharge - (float)$seller->balance, 2);
            return redirect()->route('seller.wallet.index')->with('error', __('Insufficient wallet balance. Please deposit at least :curr :amount to renew or extend your plan.', [
                'curr' => $curr,
                'amount' => $needed,
            ]));
        }

        // Deduct charge from seller wallet balance
        $seller->balance = (float)$seller->balance - $planCharge;

        $now = Carbon::now();
        $prevEnd = $seller->plan_end_date ? Carbon::parse($seller->plan_end_date) : null;

        // Stacking logic: if current plan is active and in future, add duration on top of existing end date
        if ($prevEnd && $prevEnd->isFuture()) {
            $newEnd = $prevEnd->copy()->addDays($planDuration);
            $planType = 'manual_extension';
            $details = __('Early plan extension: :curr :amount deducted. Validity extended by :days days until :date.', [
                'curr' => PriceHelper::adminCurrency(),
                'amount' => number_format($planCharge, 2),
                'days' => $planDuration,
                'date' => $newEnd->format('d M Y'),
            ]);
        } else {
            $newEnd = $now->copy()->addDays($planDuration);
            $planType = 'manual_renewal';
            $details = __('Plan renewed: :curr :amount deducted for :days days until :date.', [
                'curr' => PriceHelper::adminCurrency(),
                'amount' => number_format($planCharge, 2),
                'days' => $planDuration,
                'date' => $newEnd->format('d M Y'),
            ]);
        }

        $seller->plan_status = 'active_plan';
        $seller->plan_start_date = $seller->plan_start_date ?: $now;
        $seller->plan_end_date = $newEnd;
        $seller->plan_warned_at = null;
        $seller->save();

        // Restore any products & deals hidden by plan expiry
        Item::where('vendor_id', $seller->user_id)
            ->where('is_hidden_by_plan', 1)
            ->update([
                'status' => 1,
                'is_hidden_by_plan' => 0,
            ]);

        Deal::where('vendor_id', $seller->user_id)
            ->where('is_hidden_by_plan', 1)
            ->update([
                'status' => 1,
                'is_hidden_by_plan' => 0,
            ]);

        // Record Vendor Transaction
        VendorTransaction::create([
            'seller_id' => $seller->id,
            'user_id' => $seller->user_id,
            'type' => 'plan_renewal',
            'amount' => -$planCharge,
            'balance_after' => $seller->balance,
            'details' => $details,
            'status' => 'completed',
        ]);

        // Record Vendor Plan History
        VendorPlanHistory::ensureTable();
        VendorPlanHistory::create([
            'seller_id' => $seller->id,
            'user_id' => $seller->user_id,
            'plan_type' => $planType,
            'plan_name' => 'Time-Based Store Plan',
            'duration_days' => $planDuration,
            'days_added' => $planDuration,
            'charge' => $planCharge,
            'start_date' => $prevEnd && $prevEnd->isFuture() ? $prevEnd : $now,
            'end_date' => $newEnd,
            'previous_end_date' => $prevEnd,
            'balance_after' => $seller->balance,
            'details' => __('Validity extended by :days days until :date', [
                'days' => $planDuration,
                'date' => $newEnd->format('d M Y'),
            ]),
            'status' => 'active',
        ]);

        // Vendor Notification
        VendorNotification::log(
            $seller->user_id,
            'plan_extended',
            __('Store Plan Extended Successfully!'),
            __('Your store plan has been extended by :days days until :date. Remaining validity from your previous plan was merged and retained.', [
                'days' => $planDuration,
                'date' => $newEnd->format('d M Y'),
            ]),
            route('seller.plan.index'),
            'fas fa-check-circle',
            'success'
        );

        return redirect()->route('seller.plan.index')->withSuccess(__('Store plan extended successfully! :days days have been added to your validity until :date.', [
            'days' => $planDuration,
            'date' => $newEnd->format('d M Y'),
        ]));
    }
}
