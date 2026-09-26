<?php

namespace App\Http\Controllers\Back;

use App\Helpers\PriceHelper;
use App\Http\Controllers\Controller;
use App\Models\DepositRequest;
use App\Models\Item;
use App\Models\Order;
use App\Models\Seller;
use App\Models\Setting;
use App\Models\VendorTransaction;
use Illuminate\Http\Request;

class DepositRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }

    public function index(Request $request)
    {
        $status = $request->status;
        $search = trim($request->search ?? '');
        $query = DepositRequest::with(['user', 'seller'])->latest();

        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function($q) use ($search) {
                $q->where('txn_id', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%")
                  ->orWhere('payment_method', 'like', "%{$search}%")
                  ->orWhere('account_name', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%")
                  ->orWhere('bank_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('seller', function($sq) use ($search) {
                      $sq->where('shop_name', 'like', "%{$search}%");
                  });
            });
        }

        $pendingCount = DepositRequest::where('status', 'pending')->count();
        $approvedCount = DepositRequest::where('status', 'approved')->count();
        $rejectedCount = DepositRequest::where('status', 'rejected')->count();

        $datas = $query->paginate(15);

        return view('back.deposit_request.index', compact('datas', 'pendingCount', 'approvedCount', 'rejectedCount', 'status', 'search'));
    }

    public function approve($id)
    {
        $deposit = DepositRequest::findOrFail($id);
        if ($deposit->status !== 'pending') {
            return redirect()->back()->withErrors(__('This deposit request is already processed.'));
        }

        $seller = Seller::where('user_id', $deposit->user_id)->first();
        if (!$seller && $deposit->seller_id) {
            $seller = Seller::find($deposit->seller_id);
        }

        if (!$seller) {
            return redirect()->back()->withErrors(__('Vendor store profile not found for this request.'));
        }

        // 1. Credit Vendor Balance
        $depositAmount = (float) $deposit->amount;
        $seller->balance = (float)$seller->balance + $depositAmount;
        $seller->save();

        // 2. Update Deposit Request status
        $deposit->status = 'approved';
        $deposit->save();

        // 3. Update or create VendorTransaction
        $txn = VendorTransaction::where('deposit_request_id', $deposit->id)->first();
        if ($txn) {
            $txn->update([
                'status' => 'completed',
                'balance_after' => $seller->balance,
                'details' => __('Deposit Approved via :method - :currency :amount credited (Txn ID: :txnid)', [
                    'method' => $deposit->payment_method,
                    'currency' => PriceHelper::adminCurrency(),
                    'amount' => number_format($depositAmount, 2),
                    'txnid' => $deposit->txn_id
                ])
            ]);
        } else {
            VendorTransaction::create([
                'seller_id' => $seller->id,
                'user_id' => $deposit->user_id,
                'type' => 'deposit',
                'amount' => $depositAmount,
                'balance_after' => $seller->balance,
                'deposit_request_id' => $deposit->id,
                'details' => __('Deposit Approved via :method - :currency :amount credited (Txn ID: :txnid)', [
                    'method' => $deposit->payment_method,
                    'currency' => PriceHelper::adminCurrency(),
                    'amount' => number_format($depositAmount, 2),
                    'txnid' => $deposit->txn_id
                ]),
                'status' => 'completed'
            ]);
        }

        // 4. Auto-Unlock Pending Locked Orders if Balance is Now Sufficient
        $setting = Setting::first();
        $commissionPercent = $setting ? (float) $setting->vendor_commission_percent : 2.0;

        $lockedOrders = Order::where('vendor_id', $deposit->user_id)
            ->where('is_locked', 1)
            ->orderBy('id', 'asc')
            ->get();

        $unlockedOrdersCount = 0;

        foreach ($lockedOrders as $lockedOrder) {
            $requiredCommission = (float) $lockedOrder->commission_amount;
            if ($requiredCommission <= 0) {
                // Calculate from vendor cart
                $cart = json_decode($lockedOrder->cart, true);
                $cartTotal = 0;
                if (is_array($cart)) {
                    foreach ($cart as $item) {
                        $p = $item['main_price'] ?? 0;
                        $ap = $item['attribute_price'] ?? 0;
                        $q = $item['qty'] ?? 1;
                        $cartTotal += ($p + $ap) * $q;
                    }
                }
                $requiredCommission = round(($cartTotal * $commissionPercent) / 100, 2);
            }

            // Check if seller's balance can cover this commission
            if ($seller->balance >= $requiredCommission && $requiredCommission > 0) {
                $seller->balance -= $requiredCommission;
                $seller->save();

                VendorTransaction::create([
                    'seller_id' => $seller->id,
                    'user_id' => $deposit->user_id,
                    'type' => 'commission_deduction',
                    'amount' => -$requiredCommission,
                    'balance_after' => $seller->balance,
                    'order_id' => $lockedOrder->id,
                    'details' => __('Commission deducted: :currency :amount for Order #:order', [
                        'currency' => PriceHelper::adminCurrency(),
                        'amount' => number_format($requiredCommission, 2),
                        'order' => $lockedOrder->transaction_number
                    ]),
                    'status' => 'completed'
                ]);

                $lockedOrder->is_locked = 0;
                $lockedOrder->commission_amount = $requiredCommission;
                $lockedOrder->commission_status = 'deducted';
                $lockedOrder->save();

                $unlockedOrdersCount++;
            }
        }

        $successMsg = __('Deposit request approved! :currency :amount added to vendor balance.', [
            'currency' => PriceHelper::adminCurrency(),
            'amount' => number_format($depositAmount, 2)
        ]);

        if ($unlockedOrdersCount > 0) {
            $successMsg .= ' ' . __(':count previously locked order(s) have been unlocked and commission deducted.', ['count' => $unlockedOrdersCount]);
        }

        \App\Models\VendorNotification::log(
            $deposit->user_id,
            'deposit_approved',
            __('Deposit Approved!'),
            __('Your deposit of :currency :amount has been approved and added to your store wallet.', [
                'currency' => PriceHelper::adminCurrency(),
                'amount' => number_format($depositAmount, 2)
            ]),
            route('seller.wallet.index'),
            'fas fa-wallet',
            'success'
        );

        return redirect()->back()->withSuccess($successMsg);
    }

    public function reject(Request $request, $id)
    {
        $deposit = DepositRequest::findOrFail($id);
        if ($deposit->status !== 'pending') {
            return redirect()->back()->withErrors(__('This deposit request is already processed.'));
        }

        $deposit->status = 'rejected';
        $deposit->admin_note = $request->admin_note;
        $deposit->save();

        $txn = VendorTransaction::where('deposit_request_id', $deposit->id)->first();
        if ($txn) {
            $txn->update([
                'status' => 'rejected',
                'details' => __('Deposit Request Rejected. Reason: :reason', [
                    'reason' => $request->admin_note ?: __('Rejected by Admin')
                ])
            ]);
        }

        \App\Models\VendorNotification::log(
            $deposit->user_id,
            'deposit_rejected',
            __('Deposit Request Rejected'),
            __('Your deposit of :currency :amount was rejected. Reason: :reason', [
                'currency' => PriceHelper::adminCurrency(),
                'amount' => number_format((float)$deposit->amount, 2),
                'reason' => $request->admin_note ?: __('Details did not match bank verification.')
            ]),
            route('seller.wallet.index'),
            'fas fa-exclamation-circle',
            'danger'
        );

        return redirect()->back()->withSuccess(__('Deposit request rejected successfully.'));
    }
}
