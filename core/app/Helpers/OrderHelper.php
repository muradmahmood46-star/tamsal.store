<?php

namespace App\Helpers;

use App\Models\Currency;
use App\Models\Item;
use App\Models\Notification;
use App\Models\Order;
use App\Models\PromoCode;
use App\Models\Seller;
use App\Models\Setting;
use App\Models\State;
use App\Models\TrackOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class OrderHelper
{
    /**
     * Create separate orders for each vendor/store present in the checkout cart.
     *
     * @param array $cart
     * @param array $baseData
     * @param array $options
     * @return array ['primary_order' => Order, 'orders' => Order[]]
     */
    public static function createVendorOrders($cart = null, $baseData = [], $options = [])
    {
        if (empty($cart) || !is_array($cart)) {
            $cart = Session::get('cart') ?: [];
        }

        if (empty($cart)) {
            return null;
        }

        // 1. Group cart items by vendor_id
        $vendorGroups = [];
        $overallCartTotal = 0;

        foreach ($cart as $key => $item) {
            $vendorId = 0;
            if (!empty($item['deal_id'])) {
                $dealModel = \App\Models\Deal::find($item['deal_id']);
                if ($dealModel && $dealModel->vendor_id) {
                    $vendorId = (int)$dealModel->vendor_id;
                }
            }
            if ($vendorId === 0) {
                $itemId = explode('-', $key)[0];
                $product = Item::find($itemId);
                $vendorId = ($product && $product->vendor_id) ? (int)$product->vendor_id : 0;
            }
            $vendorGroups[$vendorId][$key] = $item;

            $itemPrice = $item['main_price'] ?? 0;
            $attrPrice = $item['attribute_price'] ?? 0;
            $qty = $item['qty'] ?? 1;
            $overallCartTotal += ($itemPrice + $attrPrice) * $qty;
        }

        $createdOrders = [];
        $coupon = $options['coupon'] ?? (Session::get('coupon') ?: null);
        $totalDiscountAmount = ($coupon && isset($coupon['discount'])) ? (float)$coupon['discount'] : 0;
        $stateId = !empty($baseData['state_id']) ? $baseData['state_id'] : null;
        $stateData = ($stateId && State::whereId($stateId)->exists()) ? json_encode(State::find($stateId), true) : null;
        $isFreeClaimed = !empty($baseData['is_free_delivery_claimed']) && $baseData['is_free_delivery_claimed'] == '1';

        $checkoutRef = $baseData['checkout_ref'] ?? ('CHK-' . Carbon::now()->format('Ymd') . '-' . strtoupper(Str::random(6)));

        // 2. Loop through each vendor group and create a separate order
        foreach ($vendorGroups as $vendorId => $vendorCart) {
            $vendorCartTotal = 0;
            $vendorTax = 0;

            foreach ($vendorCart as $key => $item) {
                $itemPrice = $item['main_price'] ?? 0;
                $attrPrice = $item['attribute_price'] ?? 0;
                $qty = $item['qty'] ?? 1;
                $vendorCartTotal += ($itemPrice + $attrPrice) * $qty;

                $itemId = explode('-', $key)[0];
                $product = Item::find($itemId);
                if ($product && $product->tax) {
                    $vendorTax += $product::taxCalculate($product);
                }
            }

            // Vendor Delivery fee
            if ($isFreeClaimed) {
                $vendorDeliveryFee = 0;
            } else {
                $vendorDeliveryFee = PriceHelper::getDeliveryFee($vendorCart, $vendorCartTotal, false);
            }

            $vendorShipping = [
                'id' => 1,
                'title' => $vendorDeliveryFee > 0 ? 'Delivery Charges' : 'Free Delivery',
                'price' => $vendorDeliveryFee
            ];

            // Proportional discount for this vendor
            $vendorDiscount = [];
            if ($coupon && $overallCartTotal > 0 && $totalDiscountAmount > 0) {
                $proportionalDiscount = round(($vendorCartTotal / $overallCartTotal) * $totalDiscountAmount, 2);
                $vendorDiscount = [
                    'code' => $coupon['code'] ?? [],
                    'discount' => $proportionalDiscount
                ];
            }

            // State tax (proportional if state_id set)
            $overallStatePrice = $stateId ? PriceHelper::StatePrce($stateId, $overallCartTotal) : 0;
            $vendorStatePrice = ($overallCartTotal > 0) ? round(($vendorCartTotal / $overallCartTotal) * $overallStatePrice, 2) : 0;

            // 3. Commission Calculation & Order Locking for Vendors
            $setting = Setting::first();
            $isLocked = 0;
            $commissionAmount = 0.00;
            $commissionStatus = 'free';

            if ($vendorId > 0) {
                $seller = Seller::where('user_id', $vendorId)->first();
                $freeOrdersLimit = (int)($setting->vendor_free_orders ?? 5);
                $priorOrdersCount = Order::where('vendor_id', $vendorId)->count();

                if ($priorOrdersCount >= $freeOrdersLimit) {
                    $commissionPercent = (float)($setting->vendor_commission_percent ?? 2.0);
                    $commissionAmount = round(($vendorCartTotal * $commissionPercent) / 100, 2);

                    if ($seller && $seller->balance >= $commissionAmount && $commissionAmount > 0) {
                        // Sufficient balance: Deduct immediately and unlock
                        $seller->balance = (float)$seller->balance - $commissionAmount;
                        $seller->save();

                        $isLocked = 0;
                        $commissionStatus = 'deducted';
                    } else {
                        // Insufficient balance: Lock the order until vendor tops up wallet
                        $isLocked = 1;
                        $commissionStatus = 'pending_balance';
                    }
                }
            }

            // Prepare order attributes
            $orderData = [
                'user_id' => $baseData['user_id'] ?? (Auth::check() ? Auth::id() : 0),
                'vendor_id' => $vendorId,
                'cart' => json_encode($vendorCart, true),
                'shipping' => json_encode($vendorShipping, true),
                'discount' => json_encode($vendorDiscount, true),
                'tax' => $vendorTax,
                'state_price' => $vendorStatePrice,
                'state' => $stateData,
                'shipping_info' => is_string($baseData['shipping_info'] ?? null) ? $baseData['shipping_info'] : json_encode(Session::get('shipping_address') ?: ($baseData['shipping_info'] ?? []), true),
                'billing_info' => is_string($baseData['billing_info'] ?? null) ? $baseData['billing_info'] : json_encode(Session::get('billing_address') ?: ($baseData['billing_info'] ?? []), true),
                'payment_method' => $baseData['payment_method'] ?? 'Cash On Delivery',
                'currency_sign' => $baseData['currency_sign'] ?? PriceHelper::setCurrencySign(),
                'currency_value' => $baseData['currency_value'] ?? PriceHelper::setCurrencyValue(),
                'payment_status' => $baseData['payment_status'] ?? 'Unpaid',
                'order_status' => $baseData['order_status'] ?? 'Pending',
                'is_locked' => $isLocked,
                'commission_amount' => $commissionAmount,
                'commission_status' => $commissionStatus,
                'txnid' => $baseData['txnid'] ?? ($baseData['txn_id'] ?? null),
                'charge_id' => $baseData['charge_id'] ?? null,
                'bank_name' => $baseData['bank_name'] ?? null,
                'account_name' => $baseData['account_name'] ?? null,
                'account_number' => $baseData['account_number'] ?? null,
                'payment_screenshot' => $baseData['payment_screenshot'] ?? null,
                'checkout_ref' => $checkoutRef,
                'transaction_number' => Str::random(10)
            ];

            $order = Order::create($orderData);

            // Generate clean transaction number
            $newTxn = 'ORD-' . str_pad(Carbon::now()->format('Ymd'), 4, '0000', STR_PAD_LEFT) . '-' . $order->id;
            $order->transaction_number = $newTxn;
            $order->save();

            // If commission was deducted, record the VendorTransaction
            if ($vendorId > 0 && $commissionStatus === 'deducted' && $commissionAmount > 0 && isset($seller)) {
                \App\Models\VendorTransaction::create([
                    'seller_id' => $seller->id,
                    'user_id' => $vendorId,
                    'type' => 'commission_deduction',
                    'amount' => -$commissionAmount,
                    'balance_after' => $seller->balance,
                    'order_id' => $order->id,
                    'details' => __('Commission deducted: :currency :amount for Order #:order', [
                        'currency' => PriceHelper::adminCurrency(),
                        'amount' => number_format($commissionAmount, 2),
                        'order' => $order->transaction_number
                    ]),
                    'status' => 'completed'
                ]);
            }

            // Track Order
            TrackOrder::addTrack($order, $order->order_status ?: 'Pending');

            // PriceHelper Transaction log
            PriceHelper::Transaction($order->id, $order->transaction_number, EmailHelper::getEmail(), PriceHelper::OrderTotal($order, 'trns'));

            // Notifications
            Notification::create([
                'order_id' => $order->id,
                'user_id' => $vendorId > 0 ? $vendorId : null
            ]);

            if ($vendorId > 0) {
                \App\Models\VendorNotification::log(
                    $vendorId,
                    'order',
                    __('New Order Received!'),
                    __('You have received a new order #:order of amount :currency :amount', [
                        'order' => $order->transaction_number,
                        'currency' => PriceHelper::adminCurrency(),
                        'amount' => PriceHelper::OrderTotal($order, 'trns')
                    ]),
                    route('seller.order.invoice', $order->id),
                    'fas fa-shopping-bag',
                    'success'
                );
            }

            $createdOrders[] = $order;
        }

        // 4. Post-order actions: decrease stock & license qty, increment deal orders_count, handle coupon
        PriceHelper::LicenseQtyDecrese($cart);
        PriceHelper::stockDecrese();

        $processedDealIds = [];
        foreach ($cart as $cItem) {
            if (!empty($cItem['deal_id'])) {
                $dId = (int)$cItem['deal_id'];
                if (!in_array($dId, $processedDealIds, true)) {
                    $processedDealIds[] = $dId;
                    \App\Models\Deal::where('id', $dId)->increment('orders_count');
                }
            }
        }

        if ($coupon && !empty($coupon['code']['id'])) {
            $promo = PromoCode::find($coupon['code']['id']);
            if ($promo && $promo->no_of_times > 0) {
                $promo->no_of_times -= 1;
                $promo->save();
            }
        }

        Session::put('checkout_ref', $checkoutRef);
        Session::put('checkout_orders', $createdOrders);
        if (!empty($createdOrders)) {
            Session::put('order_id', $createdOrders[0]->id);
        }

        return [
            'checkout_ref' => $checkoutRef,
            'primary_order' => $createdOrders[0] ?? null,
            'orders' => $createdOrders
        ];
    }
}
