<?php

namespace App\Http\Controllers\Seller;

use App\Helpers\PriceHelper;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use App\Models\TrackOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'seller']);
    }

    public function index(Request $request)
    {
        $vendorId = Auth::id();
        $type = $request->type;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $query = Order::where('vendor_id', $vendorId)->latest();

        if ($type) {
            $query->where('order_status', $type);
        }

        if ($startDate && $endDate) {
            $query->whereDate('created_at', '>=', Carbon::parse($startDate))
                  ->whereDate('created_at', '<=', Carbon::parse($endDate));
        }

        $datas = $query->paginate(15);

        return view('seller.order.index', compact('datas'));
    }

    public function invoice($id)
    {
        $vendorId = Auth::id();
        $order = Order::where('id', $id)->where('vendor_id', $vendorId)->firstOrFail();

        $cart = json_decode($order->cart, true);
        $sellerCart = is_array($cart) ? $cart : [];
        $sellerSubtotal = 0;

        foreach ($sellerCart as $key => $item) {
            $price = $item['main_price'] ?? 0;
            $attrPrice = $item['attribute_price'] ?? 0;
            $qty = $item['qty'] ?? 1;
            $sellerSubtotal += ($price + $attrPrice) * $qty;
        }

        return view('seller.order.invoice', compact('order', 'sellerCart', 'sellerSubtotal'));
    }

    public function printOrder($id)
    {
        $vendorId = Auth::id();
        $order = Order::where('id', $id)->where('vendor_id', $vendorId)->firstOrFail();

        $cart = json_decode($order->cart, true);
        $sellerCart = is_array($cart) ? $cart : [];
        $sellerSubtotal = 0;

        foreach ($sellerCart as $key => $item) {
            $price = $item['main_price'] ?? 0;
            $attrPrice = $item['attribute_price'] ?? 0;
            $qty = $item['qty'] ?? 1;
            $sellerSubtotal += ($price + $attrPrice) * $qty;
        }

        return view('seller.order.print', compact('order', 'sellerCart', 'sellerSubtotal'));
    }

    public function status($id, $field, $value)
    {
        $vendorId = Auth::id();
        $order = Order::where('id', $id)->where('vendor_id', $vendorId)->firstOrFail();

        if ($order->is_locked == 1) {
            return redirect()->back()->withErrors(__('This order is locked due to insufficient wallet balance. Please top up your wallet balance to unlock and process this order.'));
        }

        if (in_array($field, ['order_status', 'payment_status'])) {
            $order->update([$field => $value]);

            if ($field == 'order_status') {
                if ($value == 'Accepted') {
                    TrackOrder::addTrack($order, 'Accepted');
                } elseif ($value == 'Send to Delivery House') {
                    TrackOrder::addTrack($order, 'Accepted');
                    TrackOrder::addTrack($order, 'Send to Delivery House');
                } elseif ($value == 'In Progress') {
                    TrackOrder::addTrack($order, 'Accepted');
                    TrackOrder::addTrack($order, 'Send to Delivery House');
                    TrackOrder::addTrack($order, 'In Progress');
                } elseif ($value == 'Delivered') {
                    TrackOrder::addTrack($order, 'Accepted');
                    TrackOrder::addTrack($order, 'Send to Delivery House');
                    TrackOrder::addTrack($order, 'In Progress');
                    TrackOrder::addTrack($order, 'Delivered');
                } elseif ($value == 'Canceled') {
                    TrackOrder::addTrack($order, 'Canceled');
                }
            }
        }

        return redirect()->back()->withSuccess(__('Order status updated successfully.'));
    }
}
