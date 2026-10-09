<?php

namespace App\Http\Controllers\Back;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\ItemPromotion;
use App\Models\PromotionPlan;
use App\Models\PromotionTag;
use App\Models\ReceivingAccount;
use App\Models\Setting;
use Illuminate\Http\Request;

class StoreSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }

    public function index()
    {
        try {
            Helper::ensureStoreTables();
            ReceivingAccount::ensureTable();
            PromotionTag::ensureTable();
            PromotionPlan::ensureTable();
            ItemPromotion::ensureTable();
        } catch (\Throwable $e) {}

        $setting = Setting::first();
        if (!$setting) {
            $setting = new Setting();
        }

        try {
            $accounts = ReceivingAccount::latest()->get();
        } catch (\Throwable $e) {
            $accounts = collect();
        }

        try {
            $promotionTags = PromotionTag::latest()->get();
        } catch (\Throwable $e) {
            $promotionTags = collect();
        }

        try {
            $promotionPlans = PromotionPlan::orderBy('days', 'asc')->get();
        } catch (\Throwable $e) {
            $promotionPlans = collect();
        }

        return view('back.store_setting.index', compact('setting', 'accounts', 'promotionTags', 'promotionPlans'));
    }

    public function update(Request $request)
    {
        $planMode = $request->input('vendor_plan_mode', 'commission');

        $rules = [
            'vendor_plan_mode' => 'required|in:commission,time_based',
            'store_opening_fee' => 'required|numeric|min:0',
            'is_store_opening_free' => 'required|in:0,1',
            'vendor_min_balance' => 'required|numeric|min:0',
        ];

        if ($planMode === 'time_based') {
            $rules['vendor_free_days'] = 'required|integer|min:1';
            $rules['vendor_plan_duration'] = 'required|integer|min:1';
            $rules['vendor_plan_charge'] = 'required|numeric|min:0';
        } else {
            $rules['vendor_free_orders'] = 'required|integer|min:0';
            $rules['vendor_commission_percent'] = 'required|numeric|min:0|max:100';
        }

        $request->validate($rules);

        $setting = Setting::first();
        if (!$setting) {
            $setting = new Setting();
        }

        $oldMode = $setting->vendor_plan_mode ?? 'commission';
        $newMode = $request->vendor_plan_mode ?? 'commission';

        $updateData = [
            'store_opening_fee' => $request->store_opening_fee,
            'is_store_opening_free' => $request->is_store_opening_free,
            'vendor_min_balance' => $request->vendor_min_balance,
            'vendor_plan_mode' => $newMode,
        ];

        if ($request->has('vendor_free_days')) {
            $updateData['vendor_free_days'] = (int)$request->vendor_free_days;
        }
        if ($request->has('vendor_plan_duration')) {
            $updateData['vendor_plan_duration'] = (int)$request->vendor_plan_duration;
        }
        if ($request->has('vendor_plan_charge')) {
            $updateData['vendor_plan_charge'] = (float)$request->vendor_plan_charge;
        }
        if ($request->has('vendor_free_orders')) {
            $updateData['vendor_free_orders'] = (int)$request->vendor_free_orders;
        }
        if ($request->has('vendor_commission_percent')) {
            $updateData['vendor_commission_percent'] = (float)$request->vendor_commission_percent;
        }

        $setting->update($updateData);

        // Transition Handling:
        if ($newMode === 'time_based') {
            $freeDays = (int)($setting->vendor_free_days ?? 30);
            if ($freeDays < 1) {
                $freeDays = 30;
            }

            // When switching to Time-Based mode, every existing approved vendor gets configured Free Time from switch date
            $activeSellers = \App\Models\Seller::where('status', 1)->get();
            foreach ($activeSellers as $s) {
                if ($oldMode !== 'time_based' || !$s->plan_end_date || \Carbon\Carbon::parse($s->plan_end_date)->isPast() || $s->plan_status === 'expired') {
                    $s->plan_status = 'free_time';
                    $s->plan_start_date = \Carbon\Carbon::now();
                    $s->plan_end_date = \Carbon\Carbon::now()->addDays($freeDays);
                    $s->plan_warned_at = null;
                    $s->save();

                    // Restore any items/deals hidden by plan
                    \App\Models\Item::where('vendor_id', $s->user_id)
                        ->where('is_hidden_by_plan', 1)
                        ->update(['status' => 1, 'is_hidden_by_plan' => 0]);

                    \App\Models\Deal::where('vendor_id', $s->user_id)
                        ->where('is_hidden_by_plan', 1)
                        ->update(['status' => 1, 'is_hidden_by_plan' => 0]);
                }
            }
        } else {
            // When switching back to Commission mode, restore any products hidden by plan
            \App\Models\Item::where('is_hidden_by_plan', 1)->update(['status' => 1, 'is_hidden_by_plan' => 0]);
            \App\Models\Deal::where('is_hidden_by_plan', 1)->update(['status' => 1, 'is_hidden_by_plan' => 0]);
        }

        return redirect()->back()->withSuccess(__('Stores rate, commission, and wallet settings updated successfully!'));
    }

    public function storeAccount(Request $request)
    {
        ReceivingAccount::ensureTable();
        $request->validate([
            'payment_method' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'note' => 'nullable|string|max:1000',
            'status' => 'required|in:0,1',
        ]);

        ReceivingAccount::create($request->all());

        return redirect()->back()->withSuccess(__('Receiving payment account added successfully!'));
    }

    public function updateAccount(Request $request, $id)
    {
        ReceivingAccount::ensureTable();
        $request->validate([
            'payment_method' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'note' => 'nullable|string|max:1000',
            'status' => 'required|in:0,1',
        ]);

        $account = ReceivingAccount::findOrFail($id);
        $account->update($request->all());

        return redirect()->back()->withSuccess(__('Receiving payment account updated successfully!'));
    }

    public function deleteAccount($id)
    {
        $account = ReceivingAccount::findOrFail($id);
        $account->delete();

        return redirect()->back()->withSuccess(__('Receiving payment account deleted successfully!'));
    }

    /* =========================================================================
       PROMOTION SETTINGS (1A: Highlight Tags & 1B: Days & Pricing Management)
       ========================================================================= */

    public function storeTag(Request $request)
    {
        PromotionTag::ensureTable();
        $request->validate([
            'name' => 'required|string|max:100|unique:promotion_tags,name',
            'status' => 'required|in:0,1',
        ], [
            'name.required' => __('Please enter tag name.'),
            'name.unique' => __('This highlight tag already exists.'),
        ]);

        PromotionTag::create([
            'name' => trim($request->name),
            'status' => (int)$request->status,
        ]);

        return redirect()->back()->withSuccess(__('Highlight tag created successfully!'));
    }

    public function updateTag(Request $request, $id)
    {
        PromotionTag::ensureTable();
        $request->validate([
            'name' => 'required|string|max:100|unique:promotion_tags,name,' . $id,
            'status' => 'required|in:0,1',
        ]);

        $tag = PromotionTag::findOrFail($id);
        $tag->update([
            'name' => trim($request->name),
            'status' => (int)$request->status,
        ]);

        return redirect()->back()->withSuccess(__('Highlight tag updated successfully!'));
    }

    public function deleteTag($id)
    {
        $tag = PromotionTag::findOrFail($id);
        $tag->delete();

        return redirect()->back()->withSuccess(__('Highlight tag deleted successfully!'));
    }

    public function storePlan(Request $request)
    {
        PromotionPlan::ensureTable();
        $request->validate([
            'days' => 'required|integer|min:1|unique:promotion_plans,days',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:0,1',
        ], [
            'days.required' => __('Please enter number of days.'),
            'days.unique' => __('A promotion option with this duration already exists.'),
            'price.required' => __('Please enter price for this duration.'),
        ]);

        PromotionPlan::create([
            'days' => (int)$request->days,
            'price' => (float)$request->price,
            'status' => (int)$request->status,
        ]);

        return redirect()->back()->withSuccess(__('Promotion duration and pricing option created successfully!'));
    }

    public function updatePlan(Request $request, $id)
    {
        $request->validate([
            'days' => 'required|integer|min:1|unique:promotion_plans,days,' . $id,
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:0,1',
        ]);

        $plan = PromotionPlan::findOrFail($id);
        $plan->update([
            'days' => (int)$request->days,
            'price' => (float)$request->price,
            'status' => (int)$request->status,
        ]);

        return redirect()->back()->withSuccess(__('Promotion duration and pricing option updated successfully!'));
    }

    public function deletePlan($id)
    {
        $plan = PromotionPlan::findOrFail($id);
        $plan->delete();

        return redirect()->back()->withSuccess(__('Promotion duration and pricing option deleted successfully!'));
    }
}
