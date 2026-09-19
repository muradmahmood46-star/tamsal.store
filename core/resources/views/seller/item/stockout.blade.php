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
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mob-stack" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
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
                                <td data-label="{{ __('Name') }}">
                                    <strong>{{ $data->name }}</strong>
                                    <div class="small text-muted">{{ $data->category ? $data->category->name : '' }}</div>
                                </td>
                                <td data-label="{{ __('Price') }}">
                                    <strong>{{ PriceHelper::setCurrencyPrice($data->discount_price) }}</strong>
                                </td>
                                <td data-label="{{ __('Stock') }}">
                                    <span class="badge badge-danger font-weight-bold">{{ __('Out of Stock') }}</span>
                                </td>
                                <td data-label="{{ __('Status') }}">
                                    @if($data->status == 1)
                                        <span class="badge badge-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td data-label="{{ __('Actions') }}">
                                    <a href="{{ route('seller.item.edit', $data->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-edit"></i> {{ __('Update Stock') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
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
