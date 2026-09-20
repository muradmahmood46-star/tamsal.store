@include('includes.order_slip', [
    'order' => $order,
    'slipCart' => $sellerCart,
    'storeName' => (Auth::user() && Auth::user()->seller) ? Auth::user()->seller->shop_name : ($order->seller ? $order->seller->shop_name : __('Seller Store')),
    'storeEmail' => Auth::user() ? Auth::user()->email : ($order->seller ? $order->seller->email : ''),
    'storePhone' => Auth::user() ? Auth::user()->phone : '',
])
