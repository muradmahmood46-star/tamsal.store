<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\DepositRequest;
use App\Models\ReceivingAccount;
use App\Models\Seller;
use App\Models\Setting;
use App\Models\VendorTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'seller']);
    }

    public function index()
    {
        $user = Auth::user();
        $seller = Seller::where('user_id', $user->id)->first();
        $setting = Setting::first();
        $receivingAccounts = ReceivingAccount::where('status', 1)->get();
        $depositRequests = DepositRequest::where('user_id', $user->id)->latest()->paginate(10);
        $recentTransactions = VendorTransaction::where('user_id', $user->id)->latest()->take(5)->get();

        return view('seller.wallet.index', compact(
            'user',
            'seller',
            'setting',
            'receivingAccounts',
            'depositRequests',
            'recentTransactions'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $seller = Seller::where('user_id', $user->id)->firstOrFail();
        $setting = Setting::first();
        $minBalance = $setting ? (float) $setting->vendor_min_balance : 0;

        $request->validate([
            'payment_method' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'amount' => 'required|numeric|min:' . ($minBalance > 0 ? $minBalance : 1),
            'txn_id' => 'required|string|max:255',
            'screenshot' => 'required|file|mimes:jpeg,jpg,png,webp,svg,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif|max:10240',
        ], [
            'bank_name.required' => __('Please enter the bank / wallet title you sent the payment from (e.g. Easypaisa, HBL).'),
            'amount.min' => __('Minimum balance to add is :currency :min. You cannot add less than this.', [
                'currency' => \App\Helpers\PriceHelper::adminCurrency(),
                'min' => $minBalance
            ]),
            'screenshot.required' => __('Please upload a payment screenshot/proof of your deposit.'),
        ]);

        $screenshotFilename = null;
        if ($request->hasFile('screenshot')) {
            $uploadDir = public_path('storage/images/deposits');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $file = $request->file('screenshot');
            $ext = $file->getClientOriginalExtension();
            $screenshotFilename = time() . '_' . Str::random(10) . '.' . $ext;
            $file->move($uploadDir, $screenshotFilename);
        }

        $deposit = DepositRequest::create([
            'seller_id' => $seller->id,
            'user_id' => $user->id,
            'payment_method' => $request->payment_method,
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'amount' => $request->amount,
            'txn_id' => $request->txn_id,
            'screenshot' => $screenshotFilename,
            'status' => 'pending',
        ]);

        VendorTransaction::create([
            'seller_id' => $seller->id,
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => $request->amount,
            'balance_after' => $seller->balance,
            'deposit_request_id' => $deposit->id,
            'details' => __('Deposit Request Submitted via :method (Txn ID: :txnid)', [
                'method' => $request->payment_method,
                'txnid' => $request->txn_id
            ]),
            'status' => 'pending'
        ]);

        return redirect()->route('seller.wallet.index')->withSuccess(
            __('Deposit request for :currency :amount has been submitted successfully and is pending admin approval.', [
                'currency' => \App\Helpers\PriceHelper::adminCurrency(),
                'amount' => $request->amount
            ])
        );
    }
}
