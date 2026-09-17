@extends('master.back')

@section('styles')
<style>
    .vendor-prod-table-wrapper {
        width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        margin-bottom: 1rem;
        border: 1px solid #ebedf2;
        border-radius: 4px;
    }
    .vendor-prod-table-wrapper::-webkit-scrollbar {
        height: 6px;
    }
    .vendor-prod-table-wrapper::-webkit-scrollbar-thumb {
        background: #ced4da;
        border-radius: 4px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h3 class="mb-0 text-dark"><b><i class="fas fa-boxes text-primary mr-2"></i> {{ __('Vendor Products Review & Approval') }}</b></h3>
                    <p class="text-muted small mb-0">{{ __('Review, inspect, approve, or reject product submissions from marketplace vendors.') }}</p>
                </div>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- Filter Nav Tabs & Search -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <ul class="nav nav-pills card-header-pills">
                        <li class="nav-item">
                            <a class="nav-link {{ empty($status) || $status == 'all' ? 'active' : '' }}" 
                               href="{{ route('back.vendor_product.index') }}">
                               {{ __('All Submissions') }} <span class="badge badge-light ml-1">{{ $counts['all'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $status == 'Pending' ? 'active bg-warning text-dark font-weight-bold' : 'text-warning' }}" 
                               href="{{ route('back.vendor_product.index', ['status' => 'Pending']) }}">
                               <i class="fas fa-clock mr-1"></i> {{ __('Pending Review') }} 
                               <span class="badge {{ $status == 'Pending' ? 'badge-dark' : 'badge-warning text-dark' }} ml-1">{{ $counts['pending'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $status == 'Approved' ? 'active bg-success text-white font-weight-bold' : 'text-success' }}" 
                               href="{{ route('back.vendor_product.index', ['status' => 'Approved']) }}">
                               <i class="fas fa-check mr-1"></i> {{ __('Approved / Live') }} 
                               <span class="badge {{ $status == 'Approved' ? 'badge-light text-dark' : 'badge-success' }} ml-1">{{ $counts['approved'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $status == 'Rejected' ? 'active bg-danger text-white font-weight-bold' : 'text-danger' }}" 
                               href="{{ route('back.vendor_product.index', ['status' => 'Rejected']) }}">
                               <i class="fas fa-times mr-1"></i> {{ __('Rejected') }} 
                               <span class="badge {{ $status == 'Rejected' ? 'badge-light text-dark' : 'badge-danger' }} ml-1">{{ $counts['rejected'] }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-4 mt-3 mt-md-0">
                    <form action="{{ route('back.vendor_product.index') }}" method="GET">
                        @if(!empty($status))
                            <input type="hidden" name="status" value="{{ $status }}">
                        @endif
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="{{ __('Search by product, SKU, vendor...') }}" value="{{ $search ?? '' }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                                @if(!empty($search))
                                    <a href="{{ route('back.vendor_product.index', ['status' => $status]) }}" class="btn btn-secondary" title="{{ __('Clear Search') }}"><i class="fas fa-times"></i></a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive vendor-prod-table-wrapper">
                <table class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 70px;">{{ __('Image') }}</th>
                            <th>{{ __('Product Info') }}</th>
                            <th>{{ __('Vendor / Store') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Submitted Date') }}</th>
                            <th style="min-width: 170px;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($datas as $item)
                            <tr>
                                <td class="text-center align-middle">
                                    <img src="{{ $item->photo ? asset('core/public/storage/images/' . $item->photo) : asset('core/public/storage/images/placeholder.png') }}" 
                                         alt="{{ $item->name }}" 
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                                </td>
                                <td>
                                    <h6 class="mb-1 font-weight-bold text-dark">{{ $item->name }}</h6>
                                    <div class="small text-muted mb-1">
                                        <span class="mr-2"><strong>SKU:</strong> {{ $item->sku ?: 'N/A' }}</span> | 
                                        <span class="ml-2"><strong>Category:</strong> {{ $item->category ? $item->category->name : 'N/A' }}</span>
                                    </div>
                                    <span class="badge badge-info text-uppercase font-weight-normal" style="font-size: 10px;">{{ $item->item_type }}</span>
                                    @if($item->stock == 0 && $item->item_type == 'normal')
                                        <span class="badge badge-danger font-weight-normal" style="font-size: 10px;">{{ __('Out of stock') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <strong><i class="fas fa-store text-primary mr-1"></i> {{ $item->seller ? $item->seller->shop_name : ($item->user ? $item->user->first_name . ' ' . $item->user->last_name : 'Unknown') }}</strong>
                                    <div class="small text-muted">
                                        <div><i class="fas fa-user mr-1"></i> {{ $item->user ? $item->user->first_name . ' ' . $item->user->last_name : '-' }}</div>
                                        <div><i class="fas fa-envelope mr-1"></i> {{ $item->user ? $item->user->email : '-' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div><strong>{{ PriceHelper::setCurrencyPrice($item->discount_price) }}</strong></div>
                                    @if($item->previous_price > 0 && $item->previous_price != $item->discount_price)
                                        <small class="text-muted"><del>{{ PriceHelper::setCurrencyPrice($item->previous_price) }}</del></small>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if($item->approval_status == 'Pending')
                                        <span class="badge badge-warning text-dark font-weight-bold py-1 px-2">
                                            <i class="fas fa-clock mr-1"></i> {{ __('Pending Review') }}
                                        </span>
                                    @elseif($item->approval_status == 'Approved')
                                        <span class="badge badge-success font-weight-bold py-1 px-2">
                                            <i class="fas fa-check-circle mr-1"></i> {{ __('Approved (Live)') }}
                                        </span>
                                    @elseif($item->approval_status == 'Rejected')
                                        <span class="badge badge-danger font-weight-bold py-1 px-2 mb-1 d-inline-block">
                                            <i class="fas fa-times-circle mr-1"></i> {{ __('Rejected') }}
                                        </span>
                                        @if($item->reject_reason)
                                            <button type="button" class="btn btn-xs btn-outline-danger d-block mt-1 py-0 px-1" 
                                                    data-toggle="popover" title="{{ __('Rejection Reason') }}" 
                                                    data-content="{{ $item->reject_reason }}">
                                                <i class="fas fa-info-circle"></i> {{ __('View Reason') }}
                                            </button>
                                        @endif
                                    @endif
                                </td>
                                <td class="align-middle small">
                                    {{ $item->created_at ? $item->created_at->format('M d, Y h:i A') : '-' }}
                                    @if($item->updated_at && $item->updated_at != $item->created_at)
                                        <div class="text-muted font-italic" style="font-size: 11px;">
                                            {{ __('Updated:') }} {{ $item->updated_at->format('M d, Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <div class="btn-group btn-group-sm">
                                        <!-- Quick Preview Modal Button -->
                                        <button type="button" class="btn btn-info btn-sm view-product-btn" 
                                                data-id="{{ $item->id }}" 
                                                data-name="{{ $item->name }}"
                                                data-sku="{{ $item->sku }}"
                                                data-category="{{ $item->category ? $item->category->name : 'N/A' }}"
                                                data-subcategory="{{ $item->subcategory ? $item->subcategory->name : 'None' }}"
                                                data-price="{{ PriceHelper::setCurrencyPrice($item->discount_price) }}"
                                                data-prev-price="{{ $item->previous_price > 0 ? PriceHelper::setCurrencyPrice($item->previous_price) : '' }}"
                                                data-stock="{{ $item->stock }}"
                                                data-item-type="{{ $item->item_type }}"
                                                data-photo="{{ $item->photo ? asset('core/public/storage/images/' . $item->photo) : asset('core/public/storage/images/placeholder.png') }}"
                                                data-vendor="{{ $item->seller ? $item->seller->shop_name : ($item->user ? $item->user->first_name . ' ' . $item->user->last_name : 'Unknown') }}"
                                                data-vendor-contact="{{ $item->user ? $item->user->email . ' / ' . $item->user->phone : '' }}"
                                                data-approval-status="{{ $item->approval_status }}"
                                                data-reject-reason="{{ $item->reject_reason ?? '' }}"
                                                data-created="{{ $item->created_at ? $item->created_at->format('M d, Y h:i A') : '-' }}"
                                                title="{{ __('Quick Inspect') }}">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        @if($item->approval_status != 'Approved')
                                            <!-- Accept Action Button -->
                                            <button type="button" class="btn btn-success btn-sm approve-btn" 
                                                    data-id="{{ $item->id }}" 
                                                    data-name="{{ $item->name }}"
                                                    title="{{ __('Approve / Make Live') }}">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif

                                        @if($item->approval_status != 'Rejected')
                                            <!-- Reject Action Button -->
                                            <button type="button" class="btn btn-warning btn-sm reject-btn text-dark" 
                                                    data-id="{{ $item->id }}" 
                                                    data-name="{{ $item->name }}"
                                                    title="{{ __('Reject Product') }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif

                                        <!-- Delete Action Button -->
                                        <button type="button" class="btn btn-danger btn-sm delete-btn" 
                                                data-id="{{ $item->id }}" 
                                                data-name="{{ $item->name }}"
                                                title="{{ __('Delete Product') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-boxes fa-3x mb-3 text-muted"></i>
                                    <h5>{{ __('No vendor product submissions found.') }}</h5>
                                    <p class="mb-0">{{ __('When marketplace sellers list or update products, they will appear here for review.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $datas->appends(['status' => $status, 'search' => $search])->links() }}
            </div>
        </div>
    </div>
</div>

<!-- ================= 1. INSPECT PRODUCT MODAL ================= -->
<div class="modal fade" id="productInspectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-box-open mr-2"></i> <span id="modalProductName">{{ __('Product Details') }}</span></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-5 text-center mb-3 mb-md-0">
                        <div class="border rounded p-2 bg-light">
                            <img id="modalProductImg" src="" alt="Product" class="img-fluid rounded" style="max-height: 250px; object-fit: contain;">
                        </div>
                        <div class="mt-2 text-muted small" id="modalProductSku"></div>
                    </div>
                    <div class="col-md-7">
                        <h5 class="font-weight-bold text-dark" id="modalProductTitle"></h5>
                        
                        <div class="mb-3">
                            <span class="badge badge-primary mr-1" id="modalProductCategory"></span>
                            <span class="badge badge-secondary mr-1" id="modalProductSubcategory"></span>
                            <span class="badge badge-info text-uppercase" id="modalProductType"></span>
                        </div>

                        <div class="p-3 bg-light rounded mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <span class="text-muted small d-block">{{ __('Price:') }}</span>
                                    <h4 class="font-weight-bold text-success mb-0" id="modalProductPrice"></h4>
                                    <small class="text-muted" id="modalProductPrevPrice"></small>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted small d-block">{{ __('Stock Qty:') }}</span>
                                    <h5 class="font-weight-bold text-dark mb-0" id="modalProductStock"></h5>
                                </div>
                            </div>
                        </div>

                        <div class="border-top pt-2">
                            <h6 class="font-weight-bold text-dark mb-1"><i class="fas fa-store text-primary mr-1"></i> {{ __('Seller Information') }}</h6>
                            <p class="mb-1"><strong>{{ __('Shop Name:') }}</strong> <span id="modalProductVendor"></span></p>
                            <p class="mb-1 text-muted small"><strong>{{ __('Contact:') }}</strong> <span id="modalProductVendorContact"></span></p>
                            <p class="mb-1 text-muted small"><strong>{{ __('Submitted:') }}</strong> <span id="modalProductCreated"></span></p>
                        </div>

                        <div class="mt-2" id="modalStatusContainer">
                            <strong>{{ __('Status:') }}</strong> <span id="modalProductStatus"></span>
                        </div>

                        <div class="alert alert-danger mt-2 d-none" id="modalRejectReasonContainer">
                            <strong><i class="fas fa-exclamation-triangle"></i> {{ __('Current Rejection Note:') }}</strong>
                            <p class="mb-0 small" id="modalProductRejectReason"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- ================= 2. APPROVE PRODUCT MODAL ================= -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="approveForm" action="" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-check-circle mr-2"></i> {{ __('Approve Vendor Product') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4 text-center">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h5 class="font-weight-bold text-dark mb-2">{{ __('Are you sure you want to approve this product?') }}</h5>
                    <p class="text-muted" id="approveProductName"></p>
                    <div class="alert alert-info small text-left mb-0">
                        <i class="fas fa-info-circle mr-1"></i> {{ __('Once approved, this product will immediately become LIVE and visible to all customers on the marketplace.') }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success font-weight-bold px-4">{{ __('Yes, Approve & Make Live') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= 3. REJECT PRODUCT MODAL ================= -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="rejectForm" action="" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-times-circle mr-2"></i> {{ __('Reject Vendor Product') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-2"></i>
                        <h6 class="font-weight-bold text-dark" id="rejectProductName"></h6>
                        <p class="text-muted small">{{ __('Please provide a clear reason or instructions explaining why this product is rejected so the vendor can fix it and resubmit.') }}</p>
                    </div>

                    <div class="form-group">
                        <label for="reject_reason" class="font-weight-bold text-dark">
                            {{ __('Rejection Reason / Note to Vendor') }} <span class="text-danger">*</span>
                        </label>
                        <textarea name="reject_reason" id="reject_reason" class="form-control" rows="4" 
                                  placeholder="{{ __('e.g. Please upload higher resolution images and add more detailed product specifications.') }}" required></textarea>
                        <small class="form-text text-muted">{{ __('This message will be sent directly to the vendor\'s dashboard on this product.') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger font-weight-bold px-4">{{ __('Reject Product') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= 4. DELETE MODAL ================= -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="deleteForm" action="" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title font-weight-bold">{{ __('Delete Product') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center p-4">
                    <i class="fas fa-trash fa-3x text-danger mb-3"></i>
                    <h5>{{ __('Are you sure you want to permanently delete this product?') }}</h5>
                    <p class="text-muted font-weight-bold" id="deleteProductName"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger font-weight-bold">{{ __('Delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('[data-toggle="popover"]').popover({
        trigger: 'hover',
        placement: 'top'
    });

    // Inspect Modal
    $('.view-product-btn').on('click', function() {
        const btn = $(this);
        $('#modalProductName').text(btn.data('name'));
        $('#modalProductTitle').text(btn.data('name'));
        $('#modalProductImg').attr('src', btn.data('photo'));
        $('#modalProductSku').text('SKU: ' + (btn.data('sku') || 'N/A'));
        $('#modalProductCategory').text('Category: ' + btn.data('category'));
        $('#modalProductSubcategory').text('Subcategory: ' + btn.data('subcategory'));
        $('#modalProductType').text(btn.data('item-type'));
        $('#modalProductPrice').text(btn.data('price'));
        $('#modalProductPrevPrice').text(btn.data('prev-price') ? 'Original: ' + btn.data('prev-price') : '');
        $('#modalProductStock').text(btn.data('stock'));
        $('#modalProductVendor').text(btn.data('vendor'));
        $('#modalProductVendorContact').text(btn.data('vendor-contact'));
        $('#modalProductCreated').text(btn.data('created'));

        const status = btn.data('approval-status');
        let badgeHtml = '';
        if (status === 'Pending') {
            badgeHtml = '<span class="badge badge-warning text-dark font-weight-bold">Pending Review</span>';
        } else if (status === 'Approved') {
            badgeHtml = '<span class="badge badge-success font-weight-bold">Approved (Live)</span>';
        } else {
            badgeHtml = '<span class="badge badge-danger font-weight-bold">Rejected</span>';
        }
        $('#modalProductStatus').html(badgeHtml);

        const rejectReason = btn.data('reject-reason');
        if (rejectReason && status === 'Rejected') {
            $('#modalProductRejectReason').text(rejectReason);
            $('#modalRejectReasonContainer').removeClass('d-none');
        } else {
            $('#modalRejectReasonContainer').addClass('d-none');
        }

        $('#productInspectModal').modal('show');
    });

    // Approve Modal
    $('.approve-btn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        $('#approveProductName').text(name);
        $('#approveForm').attr('action', "{{ url('admin/vendor-products') }}/" + id + "/approve");
        $('#approveModal').modal('show');
    });

    // Reject Modal
    $('.reject-btn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        $('#rejectProductName').text(name);
        $('#reject_reason').val('');
        $('#rejectForm').attr('action', "{{ url('admin/vendor-products') }}/" + id + "/reject");
        $('#rejectModal').modal('show');
    });

    // Delete Modal
    $('.delete-btn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        $('#deleteProductName').text(name);
        $('#deleteForm').attr('action', "{{ url('admin/vendor-products') }}/" + id);
        $('#deleteModal').modal('show');
    });
});
</script>
@endsection
