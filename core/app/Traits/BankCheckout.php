<?php

namespace App\Traits;

use App\{
    Models\Order,
    Models\Setting,
    Models\TrackOrder,
    Helpers\EmailHelper,
    Helpers\PriceHelper,
    Models\Notification,
};
use App\Helpers\SmsHelper;
use App\Jobs\EmailSendJob;
use App\Models\Item;
use App\Models\PromoCode;
use App\Models\ShippingService;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

trait BankCheckout
{

    public function BankSubmit($data){
        $user = Auth::user();
        $setting = Setting::first();
        $cart = Session::get('cart');

        if (!empty($data['txn_id'])) {
            $txn_id = trim($data['txn_id']);
            if (PriceHelper::isTransactionIdAlreadyUsed($txn_id)) {
                return [
                    'status' => false,
                    'message' => __('This Transaction ID has already been used. Please enter a valid unique Transaction ID.')
                ];
            }
        }

        if (empty($cart) || !is_array($cart)) {
            return [
                'status' => false,
                'message' => __('Your cart is empty. Please add items before checkout.')
            ];
        }
        $total_tax = 0;
        $cart_total = 0;
        $total = 0;
        $option_price = 0;
        foreach($cart as $key => $item){

            $total += $item['main_price'] * $item['qty'];
            $option_price += ($item['attribute_price'] ?? 0);
            $cart_total = $total + $option_price;
            $itemId = explode('-', $key)[0];
            $cartItem = Item::find($itemId);
            if($cartItem && $cartItem->tax){
                $total_tax += $cartItem::taxCalculate($cartItem);
            }
        }
    
        $delivery_fee = 0;
        if (!PriceHelper::Digital()) {
            $shipping = null;
        }else{
            $isFreeClaimed = !empty($data['is_free_delivery_claimed']) && $data['is_free_delivery_claimed'] == '1';
            if ($isFreeClaimed) {
                $delivery_fee = 0;
            } else {
                $delivery_fee = PriceHelper::getDeliveryFee($cart, $cart_total, false);
            }
            $shipping = [
                'id' => 1,
                'title' => $delivery_fee > 0 ? 'Delivery Charges' : 'Free Delivery',
                'price' => $delivery_fee
            ];
        }
        
        $discount = [];
        if(Session::has('coupon')){
            $discount = Session::get('coupon');
        }
        $grand_total = ($cart_total + $delivery_fee) + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        if(!empty($data['is_advance_payment']) && $data['is_advance_payment'] == '1'){
            $grand_total = $grand_total - PriceHelper::getAdvancePaymentDiscount($cart);
        }
        $grand_total += PriceHelper::StatePrce($data['state_id'],$cart_total);
        $total_amount = PriceHelper::setConvertPrice($grand_total);

        $orderData['state'] =  $data['state_id'] ? json_encode(State::findOrFail($data['state_id']),true) : null;
        $orderData['cart'] = json_encode($cart,true);
        $orderData['discount'] = json_encode($discount,true);
        $orderData['shipping'] = json_encode($shipping,true);
        $orderData['tax'] = $total_tax;
        $orderData['state_price'] = PriceHelper::StatePrce($data['state_id'],$cart_total);
        $orderData['shipping_info'] = json_encode(Session::get('shipping_address'),true);
        $orderBaseData = [
            'user_id' => isset($user) ? $user->id : 0,
            'state_id' => !empty($data['state_id']) ? $data['state_id'] : null,
            'shipping_info' => json_encode(Session::get('shipping_address'), true),
            'billing_info' => json_encode(Session::get('billing_address'), true),
            'payment_method' => 'Bank Transfer',
            'payment_status' => 'Unpaid',
            'order_status' => 'Pending',
            'txnid' => $data['txn_id'] ?? null,
            'is_free_delivery_claimed' => !empty($data['is_free_delivery_claimed']) && $data['is_free_delivery_claimed'] == '1' ? '1' : '0'
        ];

        $orderResult = \App\Helpers\OrderHelper::createVendorOrders($cart, $orderBaseData);
        $order = $orderResult['primary_order'] ?? null;

        if ($order && $setting->is_twilio == 1) {
            $sms = new SmsHelper();
            $billInfo = json_decode($order->billing_info, true);
            $user_number = $billInfo['bill_phone'] ?? null;
            if ($user_number) {
                $sms->SendSms($user_number, "'purchase'", $order->transaction_number);
            }
        }

        if ($order) {
            Session::put('order_id', $order->id);
        }
        Session::forget('cart');
        Session::forget('discount');
        Session::forget('coupon');
        return [
            'status' => true
        ];
    }

}
