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
        ItemPromotion::ensureTable();
        $setting = Setting::first();
        $accounts = ReceivingAccount::latest()->get();
        $promotionTags = PromotionTag::latest()->get();
        $promotionPlans = PromotionPlan::orderBy('days', 'asc')->get();

        return view('back.store_setting.index', compact('setting', 'accounts', 'promotionTags', 'promotionPlans'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'store_opening_fee' => 'required|numeric|min:0',
            'is_store_opening_free' => 'required|in:0,1',
            'vendor_free_orders' => 'required|integer|min:0',
            'vendor_min_balance' => 'required|numeric|min:0',
            'vendor_commission_percent' => 'required|numeric|min:0|max:100',
        ]);

        $setting = Setting::first();
        if ($setting) {
            $setting->update([
                'store_opening_fee' => $request->store_opening_fee,
                'is_store_opening_free' => $request->is_store_opening_free,
                'vendor_free_orders' => $request->vendor_free_orders,
                'vendor_min_balance' => $request->vendor_min_balance,
                'vendor_commission_percent' => $request->vendor_commission_percent,
            ]);
        }

        return redirect()->back()->withSuccess(__('Stores rate, commission, and wallet settings updated successfully!'));
    }

    public function storeAccount(Request $request)
    {
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
