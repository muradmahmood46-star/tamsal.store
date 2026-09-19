<?php

namespace App\Helpers;

use App\Models\AttributeOption;
use App\Models\Currency;
use App\Models\Item;
use App\Models\PaymentSetting;
use App\Models\Setting;
use App\Models\State;
use App\Models\Transaction;
use Illuminate\Support\Facades\Session;

class PriceHelper
{

    public static function parsePrice($price)
    {
        if (is_numeric($price)) {
            return (float)$price;
        }
        if (is_string($price)) {
            $cleaned = trim($price);
            $cleaned = preg_replace('/^(?:rs|pkr|usd|eur|gbp|inr)[\.\s:]*/i', '', $cleaned);
            $cleaned = str_replace(',', '', $cleaned);
            if (preg_match('/-?\d+(?:\.\d+)?/', $cleaned, $matches)) {
                return (float)$matches[0];
            }
        }
        return 0.0;
    }

    public static function setPrice($price)
    {
        $curr = Currency::where('is_default', 1)->first();
        $currVal = $curr ? self::parsePrice($curr->value) : 1;
        return round(self::parsePrice($price) * $currVal, 2);
    }

    public static function adminCurrencyPrice($price)
    {
        $curr = Currency::where('is_default', 1)->first();
        $setting = Setting::first();
        $currVal = $curr ? self::parsePrice($curr->value) : 1;
        $currSign = $curr ? $curr->sign : '';
        $price = self::testPrice(self::parsePrice($price) * $currVal);
        if ($setting && $setting->currency_direction == 1) {
            return $currSign . $price;
        } else {
            return $price . $currSign;
        }
    }

    public static function adminCurrency()
    {
        $curr = Currency::where('is_default', 1)->first();
        return $curr ? $curr->sign : '';
    }

    public static function storePrice($price)
    {
        $curr = Currency::where('is_default', 1)->first();
        $currVal = $curr ? self::parsePrice($curr->value) : 1;
        return round(self::parsePrice($price) * $currVal, 2);
    }

    public static function storeOpeningFee($fee = null)
    {
        $setting = Setting::first();
        $fee = $fee !== null ? $fee : ($setting->store_opening_fee ?? 0);
        $price = self::testPrice(self::parsePrice($fee));
        $currSign = self::setCurrencySign();
        if ($setting && $setting->currency_direction == 1) {
            return $currSign . ' ' . $price;
        } else {
            return $price . ' ' . $currSign;
        }
    }

    public static function formatPrice($price)
    {
        $setting = Setting::first();
        $currSign = self::setCurrencySign() ?: self::adminCurrency();
        $formatted = self::testPrice(self::parsePrice($price));
        if ($setting && $setting->currency_direction == 1) {
            return $currSign . ' ' . $formatted;
        } else {
            return $formatted . ' ' . $currSign;
        }
    }

    public static function setCurrencyPrice($price)
    {
        if (Session::has('currency')) {
            $curr = Currency::find(Session::get('currency'));
        }
        if (empty($curr)) {
            $curr = Currency::where('is_default', 1)->first();
        }

        $setting = Setting::first();
        $currVal = $curr ? self::parsePrice($curr->value) : 1;
        $currSign = $curr ? $curr->sign : '';
        $price = self::testPrice(round(self::parsePrice($price) * $currVal, 2));

        if ($setting && $setting->currency_direction == 1) {
            return $currSign . $price;
        } else {
            return $price . $currSign;
        }
    }

    public static function setPreviousPrice($price)
    {
        if (Session::has('currency')) {
            $curr = Currency::find(Session::get('currency'));
        }
        if (empty($curr)) {
            $curr = Currency::where('is_default', 1)->first();
        }
        $numPrice = self::parsePrice($price);
        if ($numPrice != 0) {
            $setting = Setting::first();
            $currVal = $curr ? self::parsePrice($curr->value) : 1;
            $currSign = $curr ? $curr->sign : '';
            $price = self::testPrice($numPrice * $currVal);
            if ($setting && $setting->currency_direction == 1) {
                return $currSign . ' ' . $price;
            } else {
                return $price . ' ' . $currSign;
            }
        } else {
            $price = '';
        }

        return html_entity_decode($price);
    }

    public static function setConvertPrice($price)
    {
        if (Session::has('currency')) {
            $curr = Currency::find(Session::get('currency'));
        }
        if (empty($curr)) {
            $curr = Currency::where('is_default', 1)->first();
        }
        $currVal = $curr ? self::parsePrice($curr->value) : 1;
        return round(self::parsePrice($price) * $currVal, 2);
    }

    public static function convertPrice($price)
    {
        if (Session::has('currency')) {
            $curr = Currency::find(Session::get('currency'));
        }
        if (empty($curr)) {
            $curr = Currency::where('is_default', 1)->first();
        }
        $currVal = $curr ? self::parsePrice($curr->value) : 1;
        if ($currVal == 0) $currVal = 1;
        return round(self::parsePrice($price) / $currVal, 2);
    }

    public static function setCurrencySign()
    {
        if (Session::has('currency')) {
            $curr = Currency::find(Session::get('currency'));
        }
        if (empty($curr)) {
            $curr = Currency::where('is_default', 1)->first();
        }
        return $curr ? $curr->sign : '';
    }

    public static function setCurrencyValue()
    {
        if (Session::has('currency')) {
            $curr = Currency::find(Session::get('currency'));
        }
        if (empty($curr)) {
            $curr = Currency::where('is_default', 1)->first();
        }
        return $curr ? self::parsePrice($curr->value) : 1;
    }

    public static function setCurrencyName()
    {
        if (Session::has('currency')) {
            $curr = Currency::find(Session::get('currency'));
        }
        if (empty($curr)) {
            $curr = Currency::where('is_default', 1)->first();
        }
        return $curr ? $curr->name : '';
    }

    public static function grandCurrencyPrice($item)
    {
        if (Session::has('currency')) {
            $curr = Currency::find(Session::get('currency'));
        }
        if (empty($curr)) {
            $curr = Currency::where('is_default', 1)->first();
        }
        $currVal = $curr ? self::parsePrice($curr->value) : 1;
        $currSign = $curr ? $curr->sign : '';
        $discountPrice = self::parsePrice($item->discount_price ?? 0);
        $price = $discountPrice;

        $setting = Setting::first();
        $price = self::testPrice(round($price * $currVal, 2));

        if ($setting && $setting->currency_direction == 1) {
            return $currSign . $price;
        } else {
            return $price . $currSign;
        }
    }

    public static function grandPrice($item)
    {
        return self::parsePrice($item->discount_price ?? 0);
    }

    public static function Discount($discount)
    {
        if ($discount) {
            $discount = is_string($discount) ? json_decode($discount, true) : $discount;
        } else {
            $discount = 0;
        }
        return $discount;
    }

    public static function OrderTotal($order, $trns = null)
    {
        $cart = is_string($order->cart) ? json_decode($order->cart, true) : $order->cart;

        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        if (!empty($cart) && is_array($cart)) {
            foreach ($cart as $key => $item) {
                $mainPrice = self::parsePrice($item['main_price'] ?? 0);
                $attrPrice = self::parsePrice($item['attribute_price'] ?? 0);
                $qty = (int)($item['qty'] ?? 1);
                $total += ($mainPrice + $attrPrice) * $qty;
                $cart_total = $total;
                $itemId = explode('-', $key)[0];
                if (Item::where('id', $itemId)->exists()) {
                    $itemModel = Item::find($itemId);
                    if ($itemModel && $itemModel->tax) {
                        $total_tax += self::parsePrice($itemModel::taxCalculate($itemModel));
                    }
                }
            }
        }

        $shipping = [];
        if (is_string($order->shipping) && json_decode($order->shipping)) {
            $shipping = json_decode($order->shipping, true);
        } elseif (is_array($order->shipping)) {
            $shipping = $order->shipping;
        }

        $discount = [];
        if (is_string($order->discount) && json_decode($order->discount)) {
            $discount = json_decode($order->discount, true);
        } elseif (is_array($order->discount)) {
            $discount = $order->discount;
        }

        $shippingPrice = isset($shipping['price']) ? self::parsePrice($shipping['price']) : 0;
        $discountAmount = isset($discount['discount']) ? self::parsePrice($discount['discount']) : 0;

        $grand_total = ($cart_total + $shippingPrice) + $total_tax;
        $grand_total = $grand_total - $discountAmount;

        // Advance Payment Offer discount: only applied if NOT Cash on Delivery
        $isCod = in_array(strtolower(trim($order->payment_method ?? '')), ['cash on delivery', 'cod']);
        if (!$isCod) {
            $advDiscount = self::getAdvancePaymentDiscount($cart);
            $grand_total = $grand_total - self::parsePrice($advDiscount);
        }

        $statePrice = self::parsePrice($order->state_price ?? 0);
        $grand_total = $grand_total + $statePrice;

        $currencyValue = self::parsePrice($order->currency_value ?? 1);
        if ($currencyValue <= 0) {
            $currencyValue = 1;
        }

        $total_amount = round($grand_total * $currencyValue, 2);
        if (!$trns) {
            $total_amount = self::testPrice($total_amount);
        }

        return $total_amount;
    }

    public static function OrderTotalChart($order)
    {
        $cart = is_string($order->cart) ? json_decode($order->cart, true) : $order->cart;

        $total_tax = 0;
        $cart_total = 0;
        $total = 0;
        $option_price = 0;
        if (!empty($cart) && is_array($cart)) {
            foreach ($cart as $key => $item) {
                $mainPrice = self::parsePrice($item['main_price'] ?? 0);
                $attrPrice = self::parsePrice($item['attribute_price'] ?? 0);
                $qty = (int)($item['qty'] ?? 1);
                $total += $mainPrice * $qty;
                $option_price += $attrPrice;
                $cart_total = $total + $option_price;
                $itemId = explode('-', $key)[0];
                if (Item::where('id', $itemId)->exists()) {
                    $itemModel = Item::find($itemId);
                    if ($itemModel && $itemModel->tax) {
                        $total_tax += self::parsePrice($itemModel::taxCalculate($itemModel));
                    }
                }
            }
        }

        $shipping = [];
        if (is_string($order->shipping) && json_decode($order->shipping)) {
            $shipping = json_decode($order->shipping, true);
        } elseif (is_array($order->shipping)) {
            $shipping = $order->shipping;
        }

        $discount = [];
        if (is_string($order->discount) && json_decode($order->discount)) {
            $discount = json_decode($order->discount, true);
        } elseif (is_array($order->discount)) {
            $discount = $order->discount;
        }

        $shippingPrice = isset($shipping['price']) ? self::parsePrice($shipping['price']) : 0;
        $discountAmount = isset($discount['discount']) ? self::parsePrice($discount['discount']) : 0;

        $grand_total = ($cart_total + $shippingPrice) + $total_tax;
        $grand_total = $grand_total - $discountAmount;

        $isCod = in_array(strtolower(trim($order->payment_method ?? '')), ['cash on delivery', 'cod']);
        if (!$isCod) {
            $advDiscount = self::getAdvancePaymentDiscount($cart);
            $grand_total = $grand_total - self::parsePrice($advDiscount);
        }

        $curr = Currency::where('is_default', 1)->first();
        $currValue = $curr ? self::parsePrice($curr->value) : 1;
        $total_amount = round($grand_total * $currValue, 2);

        return $total_amount;
    }

    public static function cartTotal($cartt, $trns = null)
    {
        $total = 0;
    
        if (!empty($cartt) && is_array($cartt)) {
            foreach ($cartt as $key => $cart) {
                $mainPrice = self::parsePrice($cart['main_price'] ?? 0);
                $attrPrice = self::parsePrice($cart['attribute_price'] ?? 0);
                $qty = (int)($cart['qty'] ?? 1);
                $itemTotal = ($mainPrice + $attrPrice) * $qty;
                $total += $itemTotal;
            }
        }
    
        if (Session::has('currency')) {
            $curr = Currency::find(Session::get('currency'));
        }
        if (empty($curr)) {
            $curr = Currency::where('is_default', 1)->first();
        }
        $currVal = $curr ? self::parsePrice($curr->value) : 1;
        if ($currVal == 0) $currVal = 1;
    
        if ($trns) {
            if ($trns == 2) {
                return $total;
            }
            return round($total / $currVal, 2);
        }
    
        $price = self::testPrice($total / $currVal);
        return $price;
    }

    public static function CheckDigital()
    {
        $cart = Session::get('cart');
        $check_digital = false;
        if (!empty($cart) && is_array($cart)) {
            foreach ($cart as $key => $item) {
                if (($item['item_type'] ?? 'normal') == 'normal') {
                    $check_digital = true;
                }
            }
        }
        return $check_digital;
    }

    public static function CheckDigitalPaymentGateway()
    {
        $cart = Session::get('cart');
        $check_digital = true;
        if (!empty($cart) && is_array($cart)) {
            foreach ($cart as $key => $item) {
                if (($item['item_type'] ?? 'normal') == 'normal') {
                    $check_digital = false;
                }
            }
        }
        return $check_digital;
    }

    public static function isCartHasVendorProducts($cart = null)
    {
        $cart = $cart ?: Session::get('cart');
        if (!empty($cart) && is_array($cart)) {
            foreach ($cart as $key => $item) {
                $itemId = explode('-', $key)[0];
                $product = Item::find($itemId);
                if ($product && !empty($product->vendor_id) && (int)$product->vendor_id > 0) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function getCheckoutPaymentGateways($cart = null)
    {
        $cart = $cart ?: Session::get('cart');
        $gateways = PaymentSetting::whereStatus(1)->get();

        // If cart has any seller / vendor product, show ONLY Cash on Delivery
        if (self::isCartHasVendorProducts($cart)) {
            $codGateway = $gateways->where('unique_keyword', 'cod');
            if ($codGateway->count() > 0) {
                return $codGateway;
            }
            $fallbackCod = PaymentSetting::where('unique_keyword', 'cod')->get();
            return $fallbackCod;
        }

        // For pure digital products, COD might not be available
        if (self::CheckDigitalPaymentGateway()) {
            return $gateways->where('unique_keyword', '!=', 'cod');
        }

        return $gateways;
    }

    public static function Transaction($order_id, $txn_id, $user_email, $amount)
    {

        if (Session::has('currency')) {
            $curr = Currency::findOrFail(Session::get('currency'));
        } else {
            $curr = Currency::where('is_default', 1)->first();
        }

        $transaction = new Transaction();
        $transaction->order_id = $order_id;
        $transaction->txn_id = $txn_id;
        $transaction->user_email = $user_email;
        $transaction->amount = $amount / $curr->value;
        $transaction->currency_sign = $curr->sign;
        $transaction->currency_value = $curr->value;
        $transaction->save();

    }

    public static function GatewayText($keyword)
    {
        return PaymentSetting::where('unique_keyword', $keyword)->first()->text;
    }

    public static function GatewayData($keyword)
    {
        $setting = PaymentSetting::where('unique_keyword', $keyword)->first();
        if ($setting) {
            return $setting->convertJsonData() ?? [];
        }
        return [];
    }

    public static function DiscountPercentage($item)
    {
        if ($item->previous_price && $item->previous_price != 0) {
            $discount_price = $item->previous_price - $item->discount_price;
            $percentage = round($discount_price / $item->previous_price * 100);
            return $percentage . '%';
        }
    }

    public static function GetItemId($cart_id)
    {
        $item_id = explode('-', $cart_id);
        return $item_id[0];
    }

    public static function LicenseQtyDecrese($cart)
    {
        if (empty($cart) || !is_array($cart)) return;
        foreach ($cart as $item_id => $item) {
            if (isset($item['item_type']) && $item['item_type'] == 'license') {
                $item = Item::findOrFail(PriceHelper::GetItemId($item_id));
                $license_key_new = json_decode($item->license_key, true);
                $last_key = array_key_last($license_key_new);
                unset($license_key_new[$last_key]);
                $license_name_new = json_decode($item->license_key, true);
                unset($license_name_new[$last_key]);
                $item->license_name = json_encode($license_name_new, true);
                $item->license_key = json_encode($license_key_new, true);
                $item->update();
            }

        }
    }

    public static function stockDecrese()
    {
        $cart = Session::get('cart');
        if (empty($cart) || !is_array($cart)) return;
        foreach ($cart as $key => $item) {
            $itemId = explode('-', $key)[0];
            $main_item = Item::find($itemId);
            if (!$main_item) continue;

            if ($main_item->item_type == 'normal') {
                $current = $main_item->stock - $item['qty'];
                if ($current <= 0) {
                    $main_item->stock = 0;
                } else {
                    $main_item->stock = $current;
                }

                // Decrement item_variants combination stock if present
                if (!empty($main_item->item_variants)) {
                    $variants = json_decode($main_item->item_variants, true);
                    if (is_array($variants) && count($variants) > 0) {
                        $targetOptions = $item['attribute']['option_name'] ?? [];
                        foreach ($variants as &$v) {
                            $vColor = trim($v['color'] ?? '');
                            $vSize = trim($v['size'] ?? '');
                            
                            $matchColor = empty($vColor) || in_array($vColor, $targetOptions);
                            $matchSize = empty($vSize) || in_array($vSize, $targetOptions);
                            
                            if ($matchColor && $matchSize) {
                                $v['stock'] = (string) max(0, (int)$v['stock'] - (int)$item['qty']);
                                break;
                            }
                        }
                        unset($v);
                        $main_item->item_variants = json_encode($variants);
                    }
                }

                $main_item->update();

                if (!empty($item['options_id']) && is_array($item['options_id'])) {
                    foreach ($item['options_id'] as $id) {
                        $option = AttributeOption::find($id);
                        if ($option && $option->stock != 'unlimited') {
                            $new_stock = (int) $option->stock - $item['qty'];

                            if ($new_stock <= 0) {
                                $option->stock = '0';
                            } else {
                                $option->stock = (string) $new_stock;
                            }
                            $option->save();
                        }
                    }
                }
            }
        }
    }

    public static function testPrice($price)
    {
        $setting = Setting::first();
        $price = self::parsePrice($price);

        if ($setting && $setting->is_decimal == 1) {
            $decSep = $setting->decimal_separator ?: '.';
            $thSep = $setting->thousand_separator ?: ',';
            return number_format($price, 2, $decSep, $thSep);
        } else {
            return number_format($price);
        }
    }

    public static function Digital()
    {
        $cart = Session::get('cart');
        $return = false;
        if (!empty($cart) && is_array($cart)) {
            foreach ($cart as $item) {
                if (($item['type'] ?? '') == 'normal' || ($item['item_type'] ?? '') == 'normal') {
                    $return = true;
                }
            }
        }
        return $return;
    }

    public static function StatePrce($state_id, $grand_total)
    {
        $state_price = 0;
        if ($state_id) {
            $state = State::find($state_id);
            if ($state) {
                $grand_total = self::parsePrice($grand_total);
                $stPrice = self::parsePrice($state->price ?? 0);
                if ($state->type == 'fixed') {
                    $state_price = $stPrice;
                } else {
                    $state_price = ($grand_total * $stPrice) / 100;
                }
            }
        }

        return $state_price;
    }

    public static function checkCheckout($request)
    {
        $setting = Setting::first();
        if ($setting && $setting->is_single_checkout == 0) {
            return true;
        }

        Session::put('billing_address', $request->all());

        if (PriceHelper::CheckDigital()) {
            $shipping = [
                "ship_first_name" => $request->bill_first_name,
                "ship_last_name" => $request->bill_last_name,
                "ship_email" => $request->bill_email,
                "ship_phone" => $request->bill_phone,
                "ship_company" => $request->bill_company,
                "ship_address1" => $request->bill_address1,
                "ship_address2" => $request->bill_address2,
                "ship_zip" => $request->bill_zip,
                "ship_city" => $request->bill_city,
                "ship_country" => $request->bill_country,
            ];
        } else {
            $shipping = [
                "ship_first_name" => $request->bill_first_name,
                "ship_last_name" => $request->bill_last_name,
                "ship_email" => $request->bill_email,
                "ship_phone" => $request->bill_phone,
            ];
        }
        Session::put('shipping_address', $shipping);
    }

    public static function getAdvancePaymentDiscount($cart = null)
    {
        if (!$cart) {
            $cart = Session::get('cart');
        }
        $total_discount = 0;
        if (!empty($cart) && is_array($cart)) {
            $processedDeals = [];
            foreach ($cart as $key => $item) {
                if (!empty($item['deal_id'])) {
                    $dealId = $item['deal_id'];
                    if (!in_array($dealId, $processedDeals)) {
                        $processedDeals[] = $dealId;
                        $dealAdvDiscount = self::parsePrice($item['deal_advance_discount'] ?? 0);
                        if ($dealAdvDiscount <= 0 && class_exists(\App\Models\Deal::class)) {
                            $d = \App\Models\Deal::find($dealId);
                            if ($d && $d->advance_discount > 0) {
                                $dealAdvDiscount = self::parsePrice($d->advance_discount);
                            }
                        }
                        if ($dealAdvDiscount > 0) {
                            $curr_val = self::parsePrice(self::setCurrencyValue());
                            $total_discount += ($curr_val > 0 ? ($dealAdvDiscount / $curr_val) : $dealAdvDiscount);
                        }
                    }
                    continue;
                }

                $itemId = explode('-', $key)[0];
                $product = Item::find($itemId);
                if ($product && self::parsePrice($product->advance_payment_amount) > 0) {
                    $advAmt = self::parsePrice($product->advance_payment_amount);
                    $mainPrice = self::parsePrice($item['main_price'] ?? 0);
                    $qty = (int)($item['qty'] ?? 1);
                    if ($product->advance_payment_type == 'percentage') {
                        $total_discount += ($mainPrice * $advAmt) / 100;
                    } else {
                        $curr_val = self::parsePrice(self::setCurrencyValue());
                        $total_discount += ($curr_val > 0 ? ($advAmt / $curr_val) : $advAmt) * $qty;
                    }
                }
            }
        }
        return $total_discount;
    }

    public static function getDeliveryFee($cart = null, $cart_total = null, $checkThreshold = true)
    {
        if (!$cart) {
            $cart = Session::get('cart');
        }
        if (empty($cart) || !is_array($cart)) {
            return 0;
        }

        if ($cart_total === null) {
            $cart_total = 0;
            foreach ($cart as $key => $item) {
                $mainPrice = self::parsePrice($item['main_price'] ?? 0);
                $attrPrice = self::parsePrice($item['attribute_price'] ?? 0);
                $qty = (int)($item['qty'] ?? 1);
                $cart_total += ($mainPrice + $attrPrice) * $qty;
            }
        }

        if ($checkThreshold) {
            $freeShipping = \App\Models\ShippingService::where('id', 1)->first() ?? \App\Models\ShippingService::where('is_condition', 1)->first();
            if ($freeShipping && ($freeShipping->is_condition == 1 || $freeShipping->status == 1) && self::parsePrice($freeShipping->minimum_price) > 0) {
                if ($cart_total >= self::parsePrice($freeShipping->minimum_price)) {
                    return 0;
                }
            }
        }

        $total_delivery_fee = 0;
        $curr_val = self::parsePrice(self::setCurrencyValue());
        $processedDeals = [];
        foreach ($cart as $key => $item) {
            // A deal has one delivery setting for all of its included products.
            if (!empty($item['deal_id'])) {
                $dealId = (int) $item['deal_id'];
                if (in_array($dealId, $processedDeals, true)) {
                    continue;
                }
                $processedDeals[] = $dealId;
                if (!empty($item['deal_free_delivery'])) {
                    continue;
                }
                $total_delivery_fee += self::parsePrice($item['deal_delivery_charge'] ?? 0);
                continue;
            }
            $itemId = explode('-', $key)[0];
            $product = Item::find($itemId);
            if ($product) {
                if ($product->is_free_delivery == 1) {
                    continue;
                }
                $delFee = self::parsePrice($product->delivery_fee ?? 0);
                if ($delFee > 0) {
                    $fee = $curr_val > 0 ? ($delFee / $curr_val) : $delFee;
                    $total_delivery_fee += $fee;
                }
            }
        }
        return $total_delivery_fee;
    }

}
