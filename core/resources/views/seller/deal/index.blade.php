@extends('master.seller')
@section('content')
<div class="container-fluid">
    <div class="card mb-4"><div class="card-body d-flex justify-content-between align-items-center"><h3 class="mb-0 bc-title"><b>{{ __('Manage Deals') }}</b></h3><a class="btn btn-primary btn-sm" href="{{ route('seller.deal.create') }}"><i class="fas fa-plus"></i> {{ __('Create Deal') }}</a></div></div>
    @include('alerts.alerts')
    <div class="card shadow mb-4"><div class="card-body"><ul class="nav nav-pills nav-secondary mb-3"><li class="nav-item"><a class="nav-link active" data-toggle="pill" href="#active">{{ __('Active Deals') }}</a></li><li class="nav-item"><a class="nav-link" data-toggle="pill" href="#past">{{ __('Past / Expired Deals') }}</a></li></ul><div class="tab-content"><div class="tab-pane fade show active" id="active">@include('seller.deal.table', ['rows' => $deals->where('status', 1)->filter(fn($deal) => !$deal->isExpired())])</div><div class="tab-pane fade" id="past">@include('seller.deal.table', ['rows' => $deals->filter(fn($deal) => $deal->isExpired())])</div></div></div></div>
</div>
@endsection
