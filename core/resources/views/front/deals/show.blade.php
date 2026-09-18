@extends('master.front')
@section('title', $deal->name)
@section('content')
<div class="container padding-bottom-3x mb-2 mt-4" data-deal-end="{{ $deal->end_date->toIso8601String() }}">
    <a class="small" href="{{ route('front.deal.index') }}"><i class="icon-arrow-left"></i> {{ __('All Flash Deals') }}</a>
    <div class="card border-0 shadow-sm mt-3 mb-4"><div class="card-body">
        <span class="badge badge-warning text-dark float-right">{{ $deal->discount_badge }}</span>
        <h1 class="h3">{{ $deal->name }}</h1><p class="text-muted">{{ $deal->description }}</p>
        <div class="alert alert-danger mb-0"><strong>{{ __('Ends in:') }}</strong> <span class="deal-countdown">--</span> <span class="ml-3"><del>{{ PriceHelper::setCurrencyPrice($deal->original_price) }}</del> <strong class="text-success">{{ PriceHelper::setCurrencyPrice($deal->discounted_price) }}</strong></span></div>
    </div></div>
    <h2 class="h4 mb-3">{{ __('Included Products') }}</h2>
    <div class="row">
    @foreach($deal->dealItems as $dealItem)
        @php($item = $dealItem->item)
        @if($item)
        <div class="col-md-6 mb-4"><div class="card h-100"><div class="row no-gutters"><div class="col-4 p-2"><img class="img-fluid" style="height:140px;width:100%;object-fit:contain" src="{{ url('/core/public/storage/images/' . ($item->photo ?: $item->thumbnail)) }}" alt="{{ $item->name }}"></div><div class="col-8"><div class="card-body py-3"><h3 class="h6"><a href="{{ route('front.product', $item->slug) }}">{{ $item->name }}</a></h3><del class="small text-muted">{{ PriceHelper::setCurrencyPrice($dealItem->original_price) }}</del><strong class="d-block text-success">{{ PriceHelper::setCurrencyPrice($dealItem->discounted_price) }}</strong><div class="mt-2 d-flex"><form action="{{ route('front.cart.submit') }}" method="post" class="mr-2">@csrf<input type="hidden" name="item_id" value="{{ $item->id }}"><input type="hidden" name="deal_id" value="{{ $deal->id }}"><input type="hidden" name="quantity" value="1"><input type="hidden" name="type" value="1"><input type="hidden" name="addtocart" value="1"><button class="btn btn-outline-primary btn-sm">{{ __('Add to Cart') }}</button></form><form action="{{ route('front.cart.submit') }}" method="post">@csrf<input type="hidden" name="item_id" value="{{ $item->id }}"><input type="hidden" name="deal_id" value="{{ $deal->id }}"><input type="hidden" name="quantity" value="1"><input type="hidden" name="type" value="1"><button class="btn btn-primary btn-sm">{{ __('Buy Now') }}</button></form></div></div></div></div></div></div>
        @endif
    @endforeach
    </div>
</div>
@endsection
@section('scripts')
@include('front.deals.countdown-script')
@endsection
