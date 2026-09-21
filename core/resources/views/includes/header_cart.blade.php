@php
    $grandSubtotal = 0;
    $qty = 0;
    $option_price = 0;
    $bundleRemoveActionsShown = [];
@endphp
@if (Session::has('cart') && is_array(Session::get('cart')) && count(Session::get('cart')) > 0)
@foreach (Session::get('cart') as $key => $cart)
@php
    $grandSubtotal = ($cart['main_price'] + $grandSubtotal + $cart['attribute_price']) * $cart['qty'];
    $isBundleItem = !empty($cart['deal_id']);
    $showBundleRemoveAction = $isBundleItem && !in_array($cart['deal_id'], $bundleRemoveActionsShown);
    if ($showBundleRemoveAction) {
        $bundleRemoveActionsShown[] = $cart['deal_id'];
    }
@endphp
<div class="entry">
  <div class="entry-thumb"><a href="{{route('front.product',$cart['slug'])}}"><img src="{{url('/core/public/storage/images/'.$cart['photo'])}}" alt="Product"></a></div>
  <div class="entry-content">
    <h4 class="entry-title"><a href="{{route('front.product',$cart['slug'])}}">
        {{ Str::limit($cart['name'], 45) }}
    </a></h4>
    <span class="entry-meta">{{$cart['qty']}} x {{PriceHelper::setCurrencyPrice($cart['main_price'])}}</span>
    @if($isBundleItem)
    <span class="entry-meta text-primary">{{ __('Bundle') }}: {{ $cart['deal_name'] ?? __('Bundle Deal') }}</span>
    @endif
    @foreach ($cart['attribute']['option_name'] as $optionkey => $option_name)
    <span class="att"><em>{{$cart['attribute']['names'][$optionkey]}}:</em> {{$option_name}} ({{PriceHelper::setCurrencyPrice($cart['attribute']['option_price'][$optionkey])}})</span>
    @endforeach

 </div>
  @if(!$isBundleItem)
  <div class="entry-delete"><a href="{{route('front.cart.destroy',$key)}}"><i class="icon-x"></i></a></div>
  @elseif($showBundleRemoveAction)
  <div class="entry-delete"><a href="{{ route('front.cart.destroy', 'bundle-' . $cart['deal_id']) }}" title="{{ __('Remove entire bundle') }}"><i class="icon-x"></i></a></div>
  @endif
</div>
@endforeach
<div class="text-right">
<p class="text-gray-dark py-2 mb-0"><span class="text-muted">{{__('Subtotal')}}:</span> {{PriceHelper::setCurrencyPrice($grandSubtotal)}}</p>
</div>
<div class="d-flex justify-content-between">
<div class="w-50 d-block"><a class="btn btn-primary btn-sm mb-0" href="{{route('front.cart')}}"><span>{{__('Cart')}}</span></a></div>
<div class="w-50 d-block text-end"><a class="btn btn-primary btn-sm mb-0" href="{{route('front.checkout.billing')}}"><span>{{__('Checkout')}}</span></a></div>
</div>
@else
<div class="text-center py-3">
    <p class="text-muted mb-0">{{__('Cart empty')}}</p>
</div>
@endif
