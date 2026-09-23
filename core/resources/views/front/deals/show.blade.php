@extends('master.front')
@section('title', $deal->name)
@section('content')
@php
    $dealEndIso = '';
    if (!empty($deal->end_date)) {
        $dealEndIso = ($deal->end_date instanceof \Carbon\Carbon)
            ? $deal->end_date->toIso8601String()
            : \Carbon\Carbon::parse($deal->end_date)->toIso8601String();
    }
@endphp
<div class="container padding-bottom-3x mb-2 mt-4" data-deal-end="{{ $dealEndIso }}">
    <a class="small" href="{{ route('front.deal.index') }}"><i class="icon-arrow-left"></i> {{ __('All Bundles') }}</a>

    <div class="card border-0 shadow-sm mt-3 mb-4">
        <div class="card-body">
            @if($deal->photo)
                @php
                    $dealBanner = \Illuminate\Support\Str::startsWith($deal->photo, 'images/')
                        ? url('/core/public/storage/' . $deal->photo)
                        : url('/core/public/storage/images/' . $deal->photo);
                @endphp
                <img src="{{ $dealBanner }}" alt="{{ $deal->name }}" style="max-height:280px;object-fit:cover;width:100%;border-radius:8px;" class="mb-3">
            @endif
            <span class="badge badge-warning text-dark float-right">{{ $deal->discount_badge }}</span>
            <h1 class="h3">{{ $deal->name }}</h1>
            @if($deal->description)<p class="text-muted">{{ $deal->description }}</p>@endif
            <div class="alert alert-danger mb-0 d-flex justify-content-between align-items-center flex-wrap" style="gap:10px;">
                <div>
                    <strong>{{ __('Ends in:') }}</strong> <span class="deal-countdown">--</span>
                    <span class="ml-3">
                        <del>{{ PriceHelper::setCurrencyPrice($deal->original_price) }}</del>
                        <strong class="text-success ml-1" style="font-size:18px;">{{ PriceHelper::setCurrencyPrice($deal->discounted_price) }}</strong>
                    </span>
                </div>
                <div>
                    @if($deal->is_free_delivery)
                        <span class="badge badge-success px-2 py-1"><i class="fas fa-truck"></i> {{ __('Free Delivery') }}</span>
                    @elseif($deal->delivery_charge > 0)
                        <span class="badge badge-light border text-dark px-2 py-1"><i class="fas fa-truck text-primary"></i> {{ __('Delivery') }}: {{ PriceHelper::setCurrencyPrice($deal->delivery_charge) }}</span>
                    @endif
                </div>
            </div>

            <div class="mt-3 pt-3 border-top d-flex align-items-center flex-wrap" style="gap:12px;">
                <form action="{{ route('front.deal.add_to_cart', $deal->slug) }}" method="post" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-md"><i class="icon-shopping-cart mr-1"></i> {{ __('Add Bundle to Cart') }}</button>
                </form>
                <form action="{{ route('front.deal.add_to_cart', $deal->slug) }}" method="post" class="d-inline">
                    @csrf
                    <input type="hidden" name="buy_now" value="1">
                    <button type="submit" class="btn btn-success btn-md"><i class="fas fa-bolt mr-1"></i> {{ __('Buy Bundle Now') }}</button>
                </form>
            </div>

            @include('includes.copy_share_link', [
                'shareInputId' => 'bundle-share-link-' . $deal->id,
                'shareLabel' => __('Bundle Link'),
                'shareUrl' => route('front.deal.details', $deal->slug),
            ])
        </div>
    </div>

    <h2 class="h4 mb-3">{{ __('Included Products') }}</h2>
    <div class="row">
        @foreach($deal->dealItems as $dealItem)
            @php
                $item = $dealItem->item ?? null;
            @endphp
            @if($item)
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="row no-gutters">
                        <div class="col-4 p-2">
                            <img class="img-fluid" style="height:140px;width:100%;object-fit:contain"
                                src="{{ url('/core/public/storage/images/' . ($item->photo ?: $item->thumbnail)) }}"
                                alt="{{ $item->name }}">
                        </div>
                        <div class="col-8">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h3 class="h6 mb-0">{{ $item->name }}</h3>
                                    @if(($dealItem->quantity ?? 1) > 1)
                                        <span class="badge badge-primary ml-2 px-2 py-1 font-weight-bold text-nowrap" style="font-size:12px;"><i class="fas fa-cubes"></i> {{ __('Qty: ') }} {{ $dealItem->quantity }}</span>
                                    @endif
                                </div>
                                <del class="small text-muted">{{ PriceHelper::setCurrencyPrice($dealItem->original_price) }}</del>
                                <strong class="d-block text-success">{{ PriceHelper::setCurrencyPrice($dealItem->discounted_price) }} @if(($dealItem->quantity ?? 1) > 1)<small class="text-muted font-weight-normal">({{ __('each') }})</small>@endif</strong>
                                <a href="{{ route('front.product', $item->slug) }}" class="btn btn-outline-secondary btn-sm mt-2" target="_blank">
                                    <i class="icon-eye"></i> {{ __('View Product Details') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    </div>

    <div class="text-center mt-4 pt-3 border-top d-flex justify-content-center align-items-center flex-wrap" style="gap:15px;">
        <form action="{{ route('front.deal.add_to_cart', $deal->slug) }}" method="post" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm"><i class="icon-shopping-cart mr-1"></i> {{ __('Add Bundle to Cart') }}</button>
        </form>
        <form action="{{ route('front.deal.add_to_cart', $deal->slug) }}" method="post" class="d-inline">
            @csrf
            <input type="hidden" name="buy_now" value="1">
            <button type="submit" class="btn btn-success btn-lg px-4 shadow-sm"><i class="fas fa-bolt mr-1"></i> {{ __('Buy Bundle Now') }}</button>
        </form>
    </div>
</div>
@endsection
@section('scripts')
@include('front.deals.countdown-script')
@endsection
