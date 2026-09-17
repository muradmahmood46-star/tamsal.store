<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\DepositRequest;
use App\Models\Item;
use App\Models\Order;
use App\Models\Seller;
use App\Models\Transaction;
use App\Models\VendorTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'seller']);
    }

    public function index(Request $request)
    {
        $vendorId = Auth::id();
        $seller = Seller::where('user_id', $vendorId)->first();

        $query = VendorTransaction::with(['order', 'depositRequest', 'finePayment'])
            ->where('user_id', $vendorId)
            ->latest();

        $filterType = $request->type;
        if ($filterType && in_array($filterType, ['deposit', 'commission_deduction', 'fine_payment'])) {
            $query->where('type', $filterType);
        }

        $datas = $query->paginate(20);

        $totalDeposits = VendorTransaction::where('user_id', $vendorId)
            ->where('type', 'deposit')
            ->where('status', 'completed')
            ->sum('amount');

        $totalCommissionDeducted = abs(VendorTransaction::where('user_id', $vendorId)
            ->where('type', 'commission_deduction')
            ->where('status', 'completed')
            ->sum('amount'));

        $totalFinesPaid = VendorTransaction::where('user_id', $vendorId)
            ->where('type', 'fine_payment')
            ->where('status', 'completed')
            ->sum('amount');

        $pendingDepositsCount = DepositRequest::where('user_id', $vendorId)
            ->where('status', 'pending')
            ->count();

        return view('seller.transactions.index', compact(
            'datas',
            'seller',
            'totalDeposits',
            'totalCommissionDeducted',
            'totalFinesPaid',
            'pendingDepositsCount',
            'filterType'
        ));
    }

    public function show($id)
    {
        $vendorId = Auth::id();

        // Check if it's a VendorTransaction ID
        $vTxn = VendorTransaction::with(['order', 'depositRequest'])->where('user_id', $vendorId)->find($id);
        if ($vTxn) {
            return view('seller.transactions.show_vendor', compact('vTxn'));
        }

        // Otherwise fallback to order Transaction
        $transaction = Transaction::with('order')->findOrFail($id);
        $order = $transaction->order;

        $cart = json_decode($order->cart, true);
        $sellerCart = [];
        $sellerSubtotal = 0;

        if (is_array($cart)) {
            foreach ($cart as $key => $item) {
                $itemId = explode('-', $key)[0];
                $product = Item::find($itemId);
                if ($product && $product->vendor_id == $vendorId) {
                    $sellerCart[$key] = $item;
                    $price = $item['main_price'] ?? 0;
                    $attrPrice = $item['attribute_price'] ?? 0;
                    $qty = $item['qty'] ?? 1;
                    $sellerSubtotal += ($price + $attrPrice) * $qty;
                }
            }
        }

        if (empty($sellerCart)) {
            abort(403, __('Unauthorized.'));
        }

        return view('seller.transactions.show', compact('transaction', 'order', 'sellerCart', 'sellerSubtotal'));
    }
}
