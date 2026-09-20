@include('includes.order_slip', [
    'order' => $order,
    'slipCart' => $cart,
    'storeName' => $order->store_name,
    'storeEmail' => $order->vendorUser ? $order->vendorUser->email : '',
    'storePhone' => $order->vendorUser ? $order->vendorUser->phone : '',
])
