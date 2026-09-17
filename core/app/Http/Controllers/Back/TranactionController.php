<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\DepositRequest;
use App\Models\FinePayment;
use App\Models\StoreRequest;
use App\Helpers\PriceHelper;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TranactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }

    // ------- Unified Index -------//
    public function index(Request $request)
    {
        $type = $request->type;
        $status = $request->status;
        $search = trim($request->search ?? '');

        $allTransactions = $this->getAllTransactions();

        $counts = [
            'all' => $allTransactions->count(),
            'order' => $allTransactions->where('type', 'order')->count(),
            'fine' => $allTransactions->where('type', 'fine')->count(),
            'store_request' => $allTransactions->where('type', 'store_request')->count(),
            'deposit' => $allTransactions->where('type', 'deposit')->count(),
        ];

        $totals = [
            'all_amount' => $allTransactions->sum('amount'),
            'order_amount' => $allTransactions->where('type', 'order')->sum('amount'),
            'fine_amount' => $allTransactions->where('type', 'fine')->sum('amount'),
            'store_amount' => $allTransactions->where('type', 'store_request')->sum('amount'),
            'deposit_amount' => $allTransactions->where('type', 'deposit')->sum('amount'),
        ];

        $filtered = $allTransactions;

        // 1. Filter by Type
        if ($type && in_array($type, ['order', 'fine', 'store_request', 'deposit'])) {
            $filtered = $filtered->where('type', $type);
        }

        // 2. Filter by Status
        if ($status) {
            $statusLower = strtolower($status);
            $filtered = $filtered->filter(function($item) use ($statusLower) {
                return strtolower($item->status) === $statusLower;
            });
        }

        // 3. Filter by Search Keyword
        if ($search !== '') {
            $searchLower = strtolower($search);
            $filtered = $filtered->filter(function($item) use ($searchLower) {
                return str_contains(strtolower($item->txn_id ?? ''), $searchLower)
                    || str_contains(strtolower($item->name ?? ''), $searchLower)
                    || str_contains(strtolower($item->email ?? ''), $searchLower)
                    || str_contains(strtolower($item->phone ?? ''), $searchLower)
                    || str_contains(strtolower($item->store_name ?? ''), $searchLower)
                    || str_contains(strtolower($item->reference ?? ''), $searchLower)
                    || str_contains(strtolower($item->payment_method ?? ''), $searchLower)
                    || str_contains(strtolower($item->details ?? ''), $searchLower)
                    || str_contains((string)$item->amount, $searchLower);
            });
        }

        // Sort descending by date
        $filtered = $filtered->sortByDesc(function($item) {
            return $item->created_at ? $item->created_at->timestamp : 0;
        })->values();

        // Pagination (20 per page)
        $perPage = 20;
        $currentPage = LengthAwarePaginator::resolveCurrentPage() ?: 1;
        $currentItems = $filtered->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $datas = new LengthAwarePaginator($currentItems, $filtered->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => $request->query()
        ]);

        return view('back.transactions.index', compact('datas', 'counts', 'totals', 'type', 'status', 'search'));
    }

    // ------- Show / Details -------//
    public function show($id)
    {
        // Check if fine payment
        if (str_starts_with($id, 'fine_')) {
            $rawId = str_replace('fine_', '', $id);
            $fine = FinePayment::with(['user', 'seller', 'unblockRequest'])->findOrFail($rawId);
            return view('back.transactions.show_unified', [
                'item' => $this->mapFinePayment($fine),
                'raw' => $fine,
                'type' => 'fine'
            ]);
        }

        // Check if store request
        if (str_starts_with($id, 'store_')) {
            $rawId = str_replace('store_', '', $id);
            $storeReq = StoreRequest::with('user')->findOrFail($rawId);
            return view('back.transactions.show_unified', [
                'item' => $this->mapStoreRequest($storeReq),
                'raw' => $storeReq,
                'type' => 'store_request'
            ]);
        }

        // Check if deposit request
        if (str_starts_with($id, 'deposit_')) {
            $rawId = str_replace('deposit_', '', $id);
            $deposit = DepositRequest::with(['user', 'seller'])->findOrFail($rawId);
            return view('back.transactions.show_unified', [
                'item' => $this->mapDepositRequest($deposit),
                'raw' => $deposit,
                'type' => 'deposit'
            ]);
        }

        // Default: Order Transaction
        $rawId = str_replace('ord_', '', $id);
        $transaction = Transaction::with('order')->findOrFail($rawId);
        $order = $transaction->order;
        $cart = $order ? json_decode($order->cart, true) : [];
        return view('back.transactions.show', compact('transaction', 'order', 'cart'));
    }

    // ------- Delete -------//
    public function Delete($id)
    {
        if (str_starts_with($id, 'fine_')) {
            $rawId = str_replace('fine_', '', $id);
            FinePayment::findOrFail($rawId)->delete();
        } elseif (str_starts_with($id, 'store_')) {
            $rawId = str_replace('store_', '', $id);
            StoreRequest::findOrFail($rawId)->delete();
        } elseif (str_starts_with($id, 'deposit_')) {
            $rawId = str_replace('deposit_', '', $id);
            DepositRequest::findOrFail($rawId)->delete();
        } else {
            $rawId = str_replace('ord_', '', $id);
            Transaction::findOrFail($rawId)->delete();
        }

        return redirect()->back()->withSuccess(__('Transaction Deleted Successfully.'));
    }

    // ------- Helper: Fetch & Aggregate All Transactions -------//
    private function getAllTransactions(): Collection
    {
        $list = collect();

        // 1. Orders
        $orderTxns = Transaction::with(['order', 'order.user', 'order.seller'])->get();
        foreach ($orderTxns as $t) {
            $list->push($this->mapOrderTransaction($t));
        }

        // 2. Fine Payments
        $fines = FinePayment::with(['user', 'seller', 'unblockRequest'])->get();
        foreach ($fines as $f) {
            $list->push($this->mapFinePayment($f));
        }

        // 3. Store Requests (where store fee > 0 or txn_id or screenshot exists)
        $storeReqs = StoreRequest::with('user')->get();
        foreach ($storeReqs as $s) {
            if (!empty($s->transaction_id) || (float)$s->store_fee > 0 || !empty($s->payment_screenshot)) {
                $list->push($this->mapStoreRequest($s));
            }
        }

        // 4. Deposit Requests
        $deposits = DepositRequest::with(['user', 'seller'])->get();
        foreach ($deposits as $d) {
            $list->push($this->mapDepositRequest($d));
        }

        return $list;
    }

    private function mapOrderTransaction(Transaction $t): object
    {
        $order = $t->order;
        $bill = $order ? json_decode($order->billing_info, true) : [];
        $customerName = '';
        if ($order && in_array($order->payment_method, ['Easypaisa', 'JazzCash', 'Bank Transfer']) && $order->account_name) {
            $customerName = $order->account_name;
        } elseif ($bill && (!empty($bill['bill_first_name']) || !empty($bill['bill_last_name']))) {
            $customerName = trim(($bill['bill_first_name'] ?? '') . ' ' . ($bill['bill_last_name'] ?? ''));
        } elseif ($order && $order->user) {
            $customerName = trim($order->user->first_name . ' ' . $order->user->last_name);
        } else {
            $customerName = 'Customer';
        }

        $storeName = '';
        if ($order && $order->seller && !empty($order->seller->shop_name)) {
            $storeName = $order->seller->shop_name;
        } elseif ($order && $order->vendor_id) {
            $storeName = 'Store #' . $order->vendor_id;
        } else {
            $storeName = 'Official Store';
        }

        $screenshotUrl = '';
        if ($order && !empty($order->payment_screenshot)) {
            if (file_exists(public_path('storage/images/' . $order->payment_screenshot))) {
                $screenshotUrl = asset('core/public/storage/images/' . $order->payment_screenshot);
            } else {
                $screenshotUrl = url('/core/public/storage/images/' . $order->payment_screenshot);
            }
        }

        return (object)[
            'id' => 'ord_' . $t->id,
            'raw_id' => $t->id,
            'type' => 'order',
            'type_label' => __('Order Payment'),
            'type_badge' => 'badge-primary',
            'type_icon' => 'fas fa-shopping-cart',
            'name' => $customerName,
            'email' => $order ? ($bill['bill_email'] ?? ($order->user->email ?? $t->email)) : $t->email,
            'phone' => $order ? ($bill['bill_phone'] ?? ($order->user->phone ?? '')) : '',
            'store_name' => $storeName,
            'store_url' => ($order && $order->vendor_id) ? route('front.catalog') . '?vendor=' . $order->vendor_id : null,
            'txn_id' => $order && $order->txnid ? $order->txnid : ($t->txn_id ?: '-'),
            'reference' => $order ? $order->transaction_number : '-',
            'payment_method' => $order ? ($order->payment_method ?: 'N/A') : 'N/A',
            'bank_name' => $order->bank_name ?? '',
            'account_name' => $order->account_name ?? '',
            'account_number' => $order->account_number ?? '',
            'amount' => (float)$t->amount,
            'currency_sign' => $t->currency_sign ?: PriceHelper::adminCurrency(),
            'status' => $order ? ($order->payment_status ?: 'Pending') : 'Pending',
            'order_status' => $order ? $order->order_status : '',
            'screenshot' => $order->payment_screenshot ?? '',
            'screenshot_url' => $screenshotUrl,
            'details' => $order ? __('Order #:no (:status)', ['no' => $order->transaction_number, 'status' => $order->order_status]) : '',
            'created_at' => $t->created_at,
            'view_url' => route('back.transaction.show', $t->id),
            'direct_url' => ($order && $order->id) ? route('back.order.invoice', $order->id) : null,
            'direct_label' => __('View Order Invoice'),
            'model' => 'Transaction',
            'raw_data' => $t
        ];
    }

    private function mapFinePayment(FinePayment $f): object
    {
        $storeName = $f->seller ? $f->seller->shop_name : ($f->unblockRequest ? $f->unblockRequest->store_name : 'Store #' . $f->user_id);
        $applicantName = $f->unblockRequest ? $f->unblockRequest->full_name : ($f->user ? $f->user->first_name . ' ' . $f->user->last_name : 'Vendor #' . $f->user_id);
        $email = $f->unblockRequest ? $f->unblockRequest->email : ($f->user ? $f->user->email : '');
        $phone = $f->unblockRequest ? $f->unblockRequest->phone : ($f->user ? $f->user->phone : '');

        $screenshotUrl = '';
        if ($f->screenshot) {
            if (file_exists(public_path('storage/images/fines/' . $f->screenshot))) {
                $screenshotUrl = asset('core/public/storage/images/fines/' . $f->screenshot);
            } else {
                $screenshotUrl = url('/core/public/storage/images/fines/' . $f->screenshot);
            }
        }

        return (object)[
            'id' => 'fine_' . $f->id,
            'raw_id' => $f->id,
            'type' => 'fine',
            'type_label' => __('Fine Approval'),
            'type_badge' => 'badge-warning text-dark font-weight-bold',
            'type_icon' => 'fas fa-file-invoice-dollar',
            'name' => $applicantName,
            'email' => $email,
            'phone' => $phone,
            'store_name' => $storeName,
            'store_url' => route('front.catalog') . '?vendor=' . $f->user_id,
            'txn_id' => $f->txn_id ?: '-',
            'reference' => 'FINE-REQ-' . ($f->store_unblock_request_id ?: $f->id),
            'payment_method' => $f->payment_method ?: 'Direct Bank / Easypaisa',
            'bank_name' => $f->bank_name ?: '',
            'account_name' => $f->account_name ?: '',
            'account_number' => $f->account_number ?: '',
            'amount' => (float)$f->fine_amount,
            'currency_sign' => PriceHelper::adminCurrency(),
            'status' => ucfirst($f->status),
            'order_status' => '',
            'screenshot' => $f->screenshot,
            'screenshot_url' => $screenshotUrl,
            'details' => $f->admin_note ? __('Note: :note', ['note' => $f->admin_note]) : __('Store Unblock Fine Payment'),
            'created_at' => $f->created_at,
            'view_url' => route('back.transaction.show', 'fine_' . $f->id),
            'direct_url' => route('back.fine_approval.index'),
            'direct_label' => __('Manage Fine Approvals'),
            'model' => 'FinePayment',
            'raw_data' => $f
        ];
    }

    private function mapStoreRequest(StoreRequest $s): object
    {
        $screenshotUrl = '';
        if ($s->payment_screenshot) {
            if (file_exists(public_path('storage/images/' . $s->payment_screenshot))) {
                $screenshotUrl = asset('core/public/storage/images/' . $s->payment_screenshot);
            } else {
                $screenshotUrl = url('/core/public/storage/images/' . $s->payment_screenshot);
            }
        }

        return (object)[
            'id' => 'store_' . $s->id,
            'raw_id' => $s->id,
            'type' => 'store_request',
            'type_label' => __('Store Request Fee'),
            'type_badge' => 'badge-info text-white',
            'type_icon' => 'fas fa-store',
            'name' => trim($s->first_name . ' ' . $s->last_name),
            'email' => $s->email,
            'phone' => $s->phone,
            'store_name' => $s->shop_name,
            'store_url' => $s->user_id ? route('front.catalog') . '?vendor=' . $s->user_id : null,
            'txn_id' => $s->transaction_id ?: '-',
            'reference' => 'STORE-REQ-' . $s->id,
            'payment_method' => $s->account_type ?: 'Direct Transfer',
            'bank_name' => '',
            'account_name' => $s->account_name ?: '',
            'account_number' => $s->account_number ?: '',
            'amount' => (float)$s->store_fee,
            'currency_sign' => PriceHelper::adminCurrency(),
            'status' => $s->status === 'Approved' ? 'Approved' : ($s->status === 'Rejected' ? 'Rejected' : 'Pending'),
            'order_status' => '',
            'screenshot' => $s->payment_screenshot,
            'screenshot_url' => $screenshotUrl,
            'details' => __('Store Application Fee: :store', ['store' => $s->shop_name]),
            'created_at' => $s->created_at,
            'view_url' => route('back.transaction.show', 'store_' . $s->id),
            'direct_url' => route('back.store_request.index'),
            'direct_label' => __('Manage Store Requests'),
            'model' => 'StoreRequest',
            'raw_data' => $s
        ];
    }

    private function mapDepositRequest(DepositRequest $d): object
    {
        $storeName = $d->seller ? $d->seller->shop_name : 'Store #' . $d->user_id;
        $userName = $d->user ? trim($d->user->first_name . ' ' . $d->user->last_name) : ($d->account_name ?: 'Vendor');
        $email = $d->user ? $d->user->email : '';
        $phone = $d->user ? $d->user->phone : '';

        $screenshotUrl = '';
        if ($d->screenshot) {
            if (file_exists(public_path('storage/images/deposits/' . $d->screenshot))) {
                $screenshotUrl = asset('core/public/storage/images/deposits/' . $d->screenshot);
            } else {
                $screenshotUrl = url('/core/public/storage/images/deposits/' . $d->screenshot);
            }
        }

        return (object)[
            'id' => 'deposit_' . $d->id,
            'raw_id' => $d->id,
            'type' => 'deposit',
            'type_label' => __('Deposit Request'),
            'type_badge' => 'badge-success text-white',
            'type_icon' => 'fas fa-hand-holding-usd',
            'name' => $userName,
            'email' => $email,
            'phone' => $phone,
            'store_name' => $storeName,
            'store_url' => route('front.catalog') . '?vendor=' . $d->user_id,
            'txn_id' => $d->txn_id ?: '-',
            'reference' => 'DEP-REQ-' . $d->id,
            'payment_method' => $d->payment_method ?: 'Direct Transfer',
            'bank_name' => $d->bank_name ?: '',
            'account_name' => $d->account_name ?: '',
            'account_number' => $d->account_number ?: '',
            'amount' => (float)$d->amount,
            'currency_sign' => PriceHelper::adminCurrency(),
            'status' => ucfirst($d->status),
            'order_status' => '',
            'screenshot' => $d->screenshot,
            'screenshot_url' => $screenshotUrl,
            'details' => $d->admin_note ? __('Note: :note', ['note' => $d->admin_note]) : __('Vendor Wallet Deposit'),
            'created_at' => $d->created_at,
            'view_url' => route('back.transaction.show', 'deposit_' . $d->id),
            'direct_url' => route('back.deposit_request.index'),
            'direct_label' => __('Manage Deposit Requests'),
            'model' => 'DepositRequest',
            'raw_data' => $d
        ];
    }
}

