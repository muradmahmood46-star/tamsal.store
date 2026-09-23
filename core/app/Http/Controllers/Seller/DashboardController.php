<?php

namespace App\Http\Controllers\Seller;

use App\Helpers\PriceHelper;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use App\Models\Seller;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\FinePayment;
use App\Models\ReceivingAccount;
use App\Models\StoreRequest;
use App\Models\StoreUnblockRequest;
use App\Models\VendorTransaction;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'seller'])->except('leaveImpersonation');
    }

    public function leaveImpersonation()
    {
        $storeName = session('impersonated_store_name', 'Store');
        session()->forget(['admin_impersonating_vendor', 'admin_id', 'admin_name', 'impersonated_store_id', 'impersonated_store_name', 'impersonated_vendor_id']);
        Auth::guard('web')->logout();

        return redirect()->route('back.stores.index')->withSuccess(__('Switched back from store ":store" to Admin Panel successfully.', ['store' => $storeName]));
    }

    public function blocked()
    {
        if (!Auth::user()->isSellerBlocked()) {
            return redirect()->route('seller.dashboard');
        }
        $seller = Seller::where('user_id', Auth::id())->first();
        $unblockRequest = StoreUnblockRequest::where('user_id', Auth::id())->first();
        $receivingAccounts = ReceivingAccount::where('status', 1)->get();
        $latestFinePayment = FinePayment::where('user_id', Auth::id())->latest()->first();
        
        $conversation = Conversation::where('user_id', 0)->where('vendor_id', Auth::id())->first();
        $chatMessages = $conversation ? ChatMessage::where('conversation_id', $conversation->id)->where('deleted_by_vendor', 0)->orderBy('id', 'asc')->get() : collect([]);

        return view('seller.blocked', compact('seller', 'unblockRequest', 'chatMessages', 'receivingAccounts', 'latestFinePayment'));
    }

    public function submitFinePayment(Request $request)
    {
        $vendorId = Auth::id();
        $seller = Seller::where('user_id', $vendorId)->first();
        $unblockReq = StoreUnblockRequest::where('user_id', $vendorId)->first();

        if (!$unblockReq || (float)$unblockReq->fine_amount <= 0) {
            return redirect()->back()->withErrors(__('No fine has been imposed on your store at this time.'));
        }

        $fineAmount = (float) $unblockReq->fine_amount;

        $request->validate([
            'payment_method' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'txn_id' => 'required|string|max:255',
            'screenshot' => 'required|file|mimes:jpeg,jpg,png,webp,svg,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif|max:10240',
        ], [
            'bank_name.required' => __('Please enter your bank or wallet name (e.g. Easypaisa, JazzCash, HBL, SadaPay).'),
            'screenshot.required' => __('Please upload a clear screenshot of your fine payment proof.'),
        ]);

        $txnId = trim($request->txn_id);
        if (\App\Helpers\PriceHelper::isTransactionIdAlreadyUsed($txnId)) {
            return redirect()->back()->withInput()->withErrors(__('This Transaction ID has already been used. Please enter a valid unique Transaction ID.'));
        }

        $screenshotFilename = null;
        if ($request->hasFile('screenshot')) {
            $uploadDir = public_path('storage/images/fines');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $file = $request->file('screenshot');
            $ext = $file->getClientOriginalExtension();
            $screenshotFilename = time() . '_' . Str::random(10) . '.' . $ext;
            $file->move($uploadDir, $screenshotFilename);
        }

        $fine = FinePayment::create([
            'store_unblock_request_id' => $unblockReq->id,
            'user_id' => $vendorId,
            'seller_id' => $seller ? $seller->id : null,
            'fine_amount' => $fineAmount,
            'payment_method' => $request->payment_method,
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'txn_id' => $request->txn_id,
            'screenshot' => $screenshotFilename,
            'status' => 'pending',
        ]);

        // Update Store Unblock Request
        $unblockReq->update([
            'fine_status' => 'submitted',
            'status' => 'Pending Fine',
            'is_seen' => 0,
            'admin_seen_at' => null,
            'updated_at' => Carbon::now(),
        ]);

        // Create VendorTransaction entry
        VendorTransaction::create([
            'seller_id' => $seller ? $seller->id : null,
            'user_id' => $vendorId,
            'type' => 'fine_payment',
            'amount' => $fineAmount,
            'balance_after' => $seller ? $seller->balance : 0,
            'fine_payment_id' => $fine->id,
            'details' => __('Fine Payment Submitted via :method (Txn ID: :txnid)', [
                'method' => $request->payment_method,
                'txnid' => $request->txn_id
            ]),
            'status' => 'pending'
        ]);

        // Sync with admin direct conversation
        $curr = PriceHelper::adminCurrency();
        $conversation = Conversation::firstOrCreate([
            'user_id' => 0,
            'vendor_id' => $vendorId,
            'item_id' => null,
        ], [
            'last_message' => __('Store Fine Payment Submitted'),
            'last_message_at' => Carbon::now(),
            'user_unread_count' => 0,
            'vendor_unread_count' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'vendor',
            'sender_id' => $vendorId,
            'message' => "Fine Payment Proof Submitted:\nAmount: {$curr} " . number_format($fineAmount, 2) . "\nMethod: {$request->payment_method}\nSender: {$request->bank_name} - {$request->account_name} ({$request->account_number})\nTxn ID: {$request->txn_id}",
            'is_read' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        $conversation->update([
            'last_message' => "Fine Payment Submitted: {$curr} " . number_format($fineAmount, 2),
            'last_message_at' => Carbon::now(),
            'user_unread_count' => $conversation->user_unread_count + 1,
            'deleted_by_user' => 0,
        ]);

        return redirect()->back()->withSuccess(
            __('Your fine payment of :curr :amount has been submitted successfully (Txn ID: :txnid). Administration will verify the transaction and unblock your store.', [
                'curr' => $curr,
                'amount' => number_format($fineAmount, 2),
                'txnid' => $request->txn_id
            ])
        );
    }

    public function payFineFromWallet(Request $request)
    {
        $vendorId = Auth::id();
        $seller = Seller::where('user_id', $vendorId)->first();
        $unblockReq = StoreUnblockRequest::where('user_id', $vendorId)->first();

        if (!$unblockReq || (float)$unblockReq->fine_amount <= 0) {
            return redirect()->back()->withErrors(__('No fine has been imposed on your store at this time.'));
        }

        $fineAmount = (float)$unblockReq->fine_amount;
        $currentBalance = $seller ? (float)$seller->balance : 0;

        if ($currentBalance < $fineAmount) {
            return redirect()->back()->withErrors(__('Insufficient balance in wallet (Available: :curr :bal, Required: :curr :fine). Please select a payment method below to pay fine.', [
                'curr' => PriceHelper::adminCurrency(),
                'bal' => number_format($currentBalance, 2),
                'fine' => number_format($fineAmount, 2),
            ]));
        }

        $newBalance = $currentBalance - $fineAmount;

        // 1. Deduct balance from seller and activate status
        if ($seller) {
            $seller->update([
                'balance' => $newBalance,
                'status' => 1
            ]);
        }

        // 2. Unblock User & restore permissions
        $user = Auth::user();
        $user->is_seller_blocked = 0;
        $user->chat_blocked = 0;
        $user->chat_warnings_count = 0;
        $user->chat_blocked_reason = null;
        $user->save();

        // 3. Restore StoreRequest
        StoreRequest::where('user_id', $user->id)->update(['seller_status' => 'Active']);

        // 4. Restore hidden products
        Item::where('vendor_id', $user->id)
            ->where('is_hidden_by_block', 1)
            ->update([
                'status' => 1,
                'is_hidden_by_block' => 0
            ]);

        // 5. Create FinePayment record (status = approved)
        $txnId = 'WAL-' . strtoupper(Str::random(10));
        $fine = FinePayment::create([
            'store_unblock_request_id' => $unblockReq->id,
            'user_id' => $vendorId,
            'seller_id' => $seller ? $seller->id : null,
            'fine_amount' => $fineAmount,
            'payment_method' => 'Store Wallet Balance',
            'bank_name' => 'Wallet Balance Deduction',
            'account_name' => $seller ? $seller->shop_name : ($user->first_name . ' ' . $user->last_name),
            'account_number' => 'ACC-' . $vendorId,
            'txn_id' => $txnId,
            'screenshot' => null,
            'status' => 'approved',
            'approved_at' => Carbon::now(),
            'admin_note' => __('Fine paid directly from store wallet balance. Store automatically unblocked.')
        ]);

        // 6. Update StoreUnblockRequest
        $curr = PriceHelper::adminCurrency();
        $unblockReq->update([
            'fine_status' => 'paid',
            'status' => 'Unblocked',
            'fine_amount' => 0,
            'unblocked_at' => Carbon::now(),
            'admin_reply' => __('Fine of :curr :amount successfully paid from wallet balance. Store and products unblocked.', [
                'curr' => $curr,
                'amount' => number_format($fineAmount, 2)
            ]),
            'admin_replied_at' => Carbon::now(),
        ]);

        // 7. Create completed VendorTransaction record
        VendorTransaction::create([
            'seller_id' => $seller ? $seller->id : null,
            'user_id' => $vendorId,
            'type' => 'fine_payment',
            'amount' => -$fineAmount,
            'balance_after' => $newBalance,
            'fine_payment_id' => $fine->id,
            'details' => __('Store Unblock Fine Paid from Wallet Balance (Txn ID: :txnid)', ['txnid' => $txnId]),
            'status' => 'completed'
        ]);

        // 8. Post confirmation notice into direct chat
        $conversation = Conversation::firstOrCreate([
            'user_id' => 0,
            'vendor_id' => $vendorId,
            'item_id' => null,
        ], [
            'last_message' => __('Fine Paid from Wallet - Store Unblocked'),
            'last_message_at' => Carbon::now(),
            'user_unread_count' => 0,
            'vendor_unread_count' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'vendor',
            'sender_id' => $vendorId,
            'message' => "Fine Payment Paid from Wallet Balance:\nAmount: {$curr} " . number_format($fineAmount, 2) . "\nTxn ID: {$txnId}\nStore has been automatically unblocked.",
            'is_read' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        $conversation->update([
            'last_message' => "Fine Paid from Wallet: {$curr} " . number_format($fineAmount, 2) . " (Store Unblocked)",
            'last_message_at' => Carbon::now(),
            'user_unread_count' => $conversation->user_unread_count + 1,
            'deleted_by_user' => 0,
        ]);

        return redirect()->route('seller.dashboard')->withSuccess(
            __('Fine of :curr :amount has been deducted from your wallet balance. Your store ":store" is now successfully unblocked and active!', [
                'curr' => $curr,
                'amount' => number_format($fineAmount, 2),
                'store' => $seller ? $seller->shop_name : 'Store'
            ])
        );
    }

    public function submitUnblockRequest(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'phone' => 'required|string|max:191',
            'message' => 'required|string|max:3000',
        ]);

        $vendorId = Auth::id();
        $seller = Seller::where('user_id', $vendorId)->first();
        $storeName = $seller ? $seller->shop_name : (Auth::user()->first_name . '\'s Store');

        $unblockReq = StoreUnblockRequest::updateOrCreate(
            ['user_id' => $vendorId],
            [
                'seller_id' => $seller ? $seller->id : null,
                'store_name' => $storeName,
                'first_name' => trim($request->first_name),
                'last_name' => trim($request->last_name),
                'email' => trim($request->email),
                'phone' => trim($request->phone),
                'message' => trim($request->message),
                'status' => 'Pending',
                'is_seen' => 0,
                'admin_seen_at' => null,
                'updated_at' => Carbon::now(),
            ]
        );

        // Sync with admin direct conversation
        $conversation = Conversation::firstOrCreate([
            'user_id' => 0,
            'vendor_id' => $vendorId,
            'item_id' => null,
        ], [
            'last_message' => __('Store Unblock Appeal Submitted'),
            'last_message_at' => Carbon::now(),
            'user_unread_count' => 0,
            'vendor_unread_count' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'vendor',
            'sender_id' => $vendorId,
            'message' => "Store Unblock Appeal:\n" . trim($request->message),
            'is_read' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        $conversation->update([
            'last_message' => "Unblock Appeal: " . trim($request->message),
            'last_message_at' => Carbon::now(),
            'user_unread_count' => $conversation->user_unread_count + 1,
            'deleted_by_user' => 0,
        ]);

        return redirect()->back()->withSuccess(__('Your store unblock request and appeal message have been submitted to Administration. You can track responses below.'));
    }

    public function index()
    {
        $vendorId = Auth::id();
        $seller = Seller::firstOrCreate(['user_id' => $vendorId], [
            'shop_name' => Auth::user()->first_name . '\'s Store',
            'shop_address' => Auth::user()->ship_address1 ?: '',
            'shop_phone' => Auth::user()->phone ?: '',
            'shop_email' => Auth::user()->email ?: '',
            'status' => 1
        ]);

        $totalProducts = Item::where('vendor_id', $vendorId)->count();
        $totalCategories = Category::where('vendor_id', $vendorId)->count();
        $totalBrands = Brand::where('vendor_id', $vendorId)->count();
        $stockOutProducts = Item::where('vendor_id', $vendorId)->where('stock', 0)->where('item_type', 'normal')->count();

        // Get seller orders directly by vendor_id
        $sellerOrdersQuery = Order::where('vendor_id', $vendorId);
        $totalOrders = (clone $sellerOrdersQuery)->count();
        $totalPendingOrders = (clone $sellerOrdersQuery)->where('order_status', 'Pending')->count();
        $totalAcceptedOrders = (clone $sellerOrdersQuery)->where('order_status', 'Accepted')->count();
        $totalSendToDeliveryOrders = (clone $sellerOrdersQuery)->where('order_status', 'Send to Delivery House')->count();
        $totalInProgressOrders = (clone $sellerOrdersQuery)->where('order_status', 'In Progress')->count();
        $totalDeliveredOrders = (clone $sellerOrdersQuery)->where('order_status', 'Delivered')->count();
        $totalCanceledOrders = (clone $sellerOrdersQuery)->where('order_status', 'Canceled')->count();

        // Calculate seller total sales and earnings from accepted / in-progress / delivered orders
        $now = Carbon::now();
        $todayDateStr = $now->toDateString();
        $startOfMonthStr = $now->copy()->startOfMonth()->toDateString();
        $startOfYearStr = $now->copy()->startOfYear()->toDateString();

        $acceptedOrders = (clone $sellerOrdersQuery)->whereIn('order_status', ['Accepted', 'Send to Delivery House', 'In Progress', 'Delivered'])->get();
        $totalSales = 0;
        $todaySales = 0;
        $thisMonthSales = 0;
        $thisYearSales = 0;

        $totalEarnings = 0;
        $todayEarnings = 0;
        $thisMonthEarnings = 0;
        $thisYearEarnings = 0;

        foreach ($acceptedOrders as $order) {
            $orderCreatedAt = Carbon::parse($order->created_at);
            $orderDateStr = $orderCreatedAt->toDateString();
            $isToday = ($orderDateStr === $todayDateStr);
            $isThisMonth = ($orderDateStr >= $startOfMonthStr && $orderDateStr <= $todayDateStr);
            $isThisYear = ($orderDateStr >= $startOfYearStr && $orderDateStr <= $todayDateStr);

            $cart = json_decode($order->cart, true);
            if (is_array($cart)) {
                foreach ($cart as $key => $item) {
                    $qty = isset($item['qty']) ? (int)$item['qty'] : 1;
                    $price = $item['main_price'] ?? 0;
                    $attrPrice = $item['attribute_price'] ?? 0;
                    $itemSales = ($price + $attrPrice) * $qty;

                    $totalSales += $itemSales;
                    if ($isToday) {
                        $todaySales += $itemSales;
                    }
                    if ($isThisMonth) {
                        $thisMonthSales += $itemSales;
                    }
                    if ($isThisYear) {
                        $thisYearSales += $itemSales;
                    }

                    $profit = 0;
                    if (isset($item['estimated_profit']) && (float)$item['estimated_profit'] > 0) {
                        $profit = (float)$item['estimated_profit'];
                    } else {
                        $itemId = (int)explode('-', $key)[0];
                        $prod = Item::find($itemId);
                        if ($prod && (float)$prod->estimated_profit > 0) {
                            $profit = (float)$prod->estimated_profit;
                        }
                    }
                    $itemEarnings = $profit * $qty;

                    $totalEarnings += $itemEarnings;
                    if ($isToday) {
                        $todayEarnings += $itemEarnings;
                    }
                    if ($isThisMonth) {
                        $thisMonthEarnings += $itemEarnings;
                    }
                    if ($isThisYear) {
                        $thisYearEarnings += $itemEarnings;
                    }
                }
            }
        }

        $pendingProductsCount = Item::where('vendor_id', $vendorId)->where('approval_status', 'Pending')->count();
        $rejectedProductsCount = Item::where('vendor_id', $vendorId)->where('approval_status', 'Rejected')->count();
        $rejectedProducts = Item::where('vendor_id', $vendorId)->where('approval_status', 'Rejected')->get();

        $recentOrders = (clone $sellerOrdersQuery)->latest()->take(10)->get();
        $recentProducts = Item::where('vendor_id', $vendorId)->latest()->take(10)->get();

        // 30-Day Monthly Sales and Monthly Earnings Series for Charts
        $dailySalesMap = [];
        $dailyEarningsMap = [];
        $thirtyDaysAgo = Carbon::now()->subDays(30)->startOfDay();

        $recentMonthOrders = (clone $sellerOrdersQuery)
            ->whereIn('order_status', ['Accepted', 'Send to Delivery House', 'In Progress', 'Delivered'])
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->get();

        foreach ($recentMonthOrders as $order) {
            $dateKey = Carbon::parse($order->created_at)->toDateString();
            if (!isset($dailySalesMap[$dateKey])) {
                $dailySalesMap[$dateKey] = 0;
                $dailyEarningsMap[$dateKey] = 0;
            }

            $cart = json_decode($order->cart, true);
            if (is_array($cart)) {
                foreach ($cart as $key => $item) {
                    $qty = isset($item['qty']) ? (int)$item['qty'] : 1;
                    $price = $item['main_price'] ?? 0;
                    $attrPrice = $item['attribute_price'] ?? 0;
                    $itemSales = ($price + $attrPrice) * $qty;

                    $profit = 0;
                    if (isset($item['estimated_profit']) && (float)$item['estimated_profit'] > 0) {
                        $profit = (float)$item['estimated_profit'];
                    } else {
                        $itemId = (int)explode('-', $key)[0];
                        $prod = Item::find($itemId);
                        if ($prod && (float)$prod->estimated_profit > 0) {
                            $profit = (float)$prod->estimated_profit;
                        }
                    }
                    $itemEarnings = $profit * $qty;

                    $dailySalesMap[$dateKey] += $itemSales;
                    $dailyEarningsMap[$dateKey] += $itemEarnings;
                }
            }
        }

        $order_days = "";
        $order_sales = "";
        $earning_days = "";
        $total_incomess = "";

        for ($i = 0; $i < 30; $i++) {
            $targetDate = Carbon::now()->subDays($i)->toDateString();
            $label = Carbon::now()->subDays($i)->format('d M');
            $daySale = $dailySalesMap[$targetDate] ?? 0;
            $dayEarning = $dailyEarningsMap[$targetDate] ?? 0;

            $order_days .= "'{$label}',";
            $order_sales .= "'{$daySale}',";
            $earning_days .= "'{$label}',";
            $total_incomess .= "'{$dayEarning}',";
        }

        $order_days = rtrim($order_days, ", ");
        $order_sales = rtrim($order_sales, ", ");
        $earning_days = rtrim($earning_days, ", ");
        $total_incomess = rtrim($total_incomess, ", ");

        return view('seller.dashboard.index', compact(
            'seller',
            'totalProducts',
            'totalCategories',
            'totalBrands',
            'pendingProductsCount',
            'rejectedProductsCount',
            'rejectedProducts',
            'stockOutProducts',
            'totalOrders',
            'totalPendingOrders',
            'totalAcceptedOrders',
            'totalSendToDeliveryOrders',
            'totalInProgressOrders',
            'totalDeliveredOrders',
            'totalCanceledOrders',
            'totalSales',
            'todaySales',
            'thisMonthSales',
            'thisYearSales',
            'totalEarnings',
            'todayEarnings',
            'thisMonthEarnings',
            'thisYearEarnings',
            'recentOrders',
            'recentProducts',
            'order_days',
            'order_sales',
            'earning_days',
            'total_incomess'
        ));
    }
}
