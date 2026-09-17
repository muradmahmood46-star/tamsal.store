@extends('master.seller')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 text-dark"><b><i class="fas fa-file-csv text-primary mr-2"></i> {{ __('CSV Import & Export') }}</b></h3>
                <a href="{{ route('seller.item.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-chevron-left mr-1"></i> {{ __('Back to Products') }}
                </a>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <div class="row">
        <!-- CSV Export Card -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-download mr-1"></i> {{ __('Export Products to CSV') }}</h6>
                </div>
                <div class="card-body p-4 text-center">
                    <i class="fas fa-file-excel fa-4x text-success mb-3"></i>
                    <h5 class="font-weight-bold">{{ __('Download All Your Products') }}</h5>
                    <p class="text-muted">
                        {{ __('Export your store inventory, SKU, pricing, and stock into a clean CSV spreadsheet.') }}
                    </p>
                    <a href="{{ route('seller.csv.export') }}" class="btn btn-success px-4 py-2 font-weight-bold">
                        <i class="fas fa-download mr-1"></i> {{ __('Download CSV File') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- CSV Info Card -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white py-3">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-info-circle mr-1"></i> {{ __('CSV Inventory Guidelines') }}</h6>
                </div>
                <div class="card-body p-4">
                    <h6 class="font-weight-bold text-dark mb-2">{{ __('Exported Columns:') }}</h6>
                    <ul class="text-muted">
                        <li><strong>ID:</strong> Unique product identifier</li>
                        <li><strong>Name:</strong> Product Title</li>
                        <li><strong>SKU:</strong> Stock Keeping Unit</li>
                        <li><strong>Discount Price:</strong> Current selling price</li>
                        <li><strong>Previous Price:</strong> Original price</li>
                        <li><strong>Stock:</strong> Current quantity available</li>
                        <li><strong>Status:</strong> Active / Inactive</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
