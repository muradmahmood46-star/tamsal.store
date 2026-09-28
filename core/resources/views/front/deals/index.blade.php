@extends('master.front')
@section('title', __('Shop Bundles'))
@section('content')
<div class="container padding-bottom-3x mb-2 mt-4 deals-index-container">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">{{ __('Shop Bundles') }}</h1>
        <a href="{{ route('front.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('Continue Shopping') }}</a>
    </div>
    <div class="alert alert-info empty-deals-alert" style="{{ $deals->isEmpty() ? '' : 'display:none;' }}">
        {{ __('There are no active deals right now.') }}
    </div>
    @if($deals->isNotEmpty())
        <div class="row deals-row">
            @foreach($deals as $deal)
                @include('front.deals.card', ['deal' => $deal, 'column' => 'col-lg-3 col-md-4 col-sm-6 mb-4'])
            @endforeach
        </div>
    @endif
</div>
@endsection
@section('scripts')
@include('front.deals.countdown-script')
@endsection
