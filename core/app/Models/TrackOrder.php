<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackOrder extends Model
{
    protected $fillable = [
        'title',
        'text',
        'order_id'
    ];

    public function order()
    {
    	return $this->belongsTo('App\Models\Order','order_id')->withDefault();
    }

    /**
     * Add tracking entry with descriptive text and duplicate check
     */
    public static function addTrack($order, $title, $customText = null)
    {
        if (is_numeric($order)) {
            $order = \App\Models\Order::find($order);
        }
        if (!$order) {
            return null;
        }

        // Format product names from cart
        $cart = json_decode($order->cart, true);
        $products = [];
        if (is_array($cart)) {
            foreach ($cart as $item) {
                $name = $item['name'] ?? 'Product';
                $qty = $item['qty'] ?? 1;
                $products[] = $name . ' (Qty: ' . $qty . ')';
            }
        }
        $productStr = !empty($products) ? implode(', ', $products) : __('Product');

        // Get store / vendor name
        $vendor = \App\Models\User::find($order->vendor_id);
        $seller = $vendor ? \App\Models\Seller::where('user_id', $vendor->id)->first() : null;
        $storeName = $seller && !empty($seller->shop_name) ? $seller->shop_name : ($vendor && !empty($vendor->first_name) ? $vendor->first_name . '\'s Store' : __('ORIVO'));

        // Default descriptive messages in English
        if (!$customText) {
            if ($title == 'Pending') {
                $customText = 'Your order has been received and is awaiting vendor confirmation.';
            } elseif ($title == 'Accepted') {
                $customText = 'Product: ' . $productStr . ' — Order has been accepted by vendor "' . $storeName . '".';
            } elseif ($title == 'Send to Delivery House') {
                $customText = 'Your order has been dispatched from seller "' . $storeName . '" and has arrived at the Delivery House.';
            } elseif ($title == 'In Progress') {
                $customText = 'Your order is out for delivery. The delivery agent will contact you shortly.';
            } elseif ($title == 'Delivered') {
                $customText = 'Congratulations! Your order has been delivered successfully.';
            } elseif ($title == 'Canceled') {
                $customText = 'This order has been canceled.';
            } else {
                $customText = $title;
            }
        }

        // Check if track already exists with this title for this order
        $existing = self::where('order_id', $order->id)->where('title', $title)->first();
        if ($existing) {
            if (empty($existing->text) && !empty($customText)) {
                $existing->update(['text' => $customText]);
            }
            return $existing;
        }

        return self::create([
            'order_id' => $order->id,
            'title' => $title,
            'text' => $customText
        ]);
    }
}
