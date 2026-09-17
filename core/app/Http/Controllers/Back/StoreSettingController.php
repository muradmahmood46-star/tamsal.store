<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
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
        $setting = Setting::first();
        $accounts = ReceivingAccount::latest()->get();

        return view('back.store_setting.index', compact('setting', 'accounts'));
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
}
