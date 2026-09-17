@extends('master.seller')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 text-dark"><b><i class="fas fa-exclamation-triangle text-danger mr-2"></i> {{ __('Stock Out Products') }}</b></h3>
                <a href="{{ route('seller.item.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-chevron-left mr-1"></i> {{ __('All Products') }}
                </a>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="gd-responsive-table">
                <table class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ __('Image') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Stock') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($datas as $data)
                            <tr>
                                <td>
                                    <img src="{{ $data->photo ? asset('core/public/storage/images/' . $data->photo) : asset('core/public/storage/images/placeholder.png') }}"
                                        alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                </td>
                                <td>
                                    <strong>{{ $data->name }}</strong>
                                    <div class="small text-muted">{{ $data->category ? $data->category->name : '' }}</div>
                                </td>
                                <td>
                                    <strong>{{ PriceHelper::setCurrencyPrice($data->discount_price) }}</strong>
                                </td>
                                <td>
                                    <span class="badge badge-danger font-weight-bold">{{ __('0 (Out of Stock)') }}</span>
                                </td>
                                <td>
                                    @if($data->status == 1)
                                        <span class="badge badge-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('seller.item.edit', $data->id) }}" class="btn btn-info btn-sm" title="{{ __('Update Stock') }}">
                                        <i class="fas fa-edit"></i> {{ __('Update Stock') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle fa-3x text-success mb-2 d-block"></i>
                                    {{ __('Great! None of your products are currently out of stock.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $datas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
