@extends('master.seller')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 text-dark"><b><i class="fab fa-product-hunt text-primary mr-2"></i> {{ __('Add Product to Your Store') }}</b></h3>
                <a href="{{ route('seller.item.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-chevron-left mr-1"></i> {{ __('Back to Products') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-md-8 offset-md-2 mb-4">
            <a href="{{ route('seller.item.create') }}" class="card card-stats card-round shadow-sm text-decoration-none hover-shadow">
                <div class="card-body">
                    <div class="text-center py-4">
                        <div class="d-inline-block">
                            <div class="icon-big text-center icon-primary bubble-shadow-small px-3">
                                <i class="fab fa-product-hunt"></i>
                            </div>
                        </div>
                        <div class="d-block mt-3">
                            <h3 class="font-weight-bold text-dark">{{ __('Add Product') }}</h3>
                            <p class="text-muted small mb-0">{{ __('Clothes, Electronics, Shoes, Accessories, etc. with inventory, pricing and shipping.') }}</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
