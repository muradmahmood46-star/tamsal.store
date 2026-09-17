@extends('master.seller')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 text-dark"><b><i class="fab fa-product-hunt text-primary mr-2"></i> {{ __('My Store Products') }}</b></h3>
                <div>
                    <a href="{{ route('seller.csv.export') }}" class="btn btn-info btn-sm mr-2">
                        <i class="fas fa-file-csv mr-1"></i> {{ __('CSV Export') }}
                    </a>
                    <a href="{{ route('seller.item.add') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> {{ __('Add Product') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- Product Filter -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('seller.item.index') }}" method="GET">
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                        <select class="form-control" name="item_type">
                            <option value="">{{ __('All Types') }}</option>
                            <option value="normal" {{ request('item_type') == 'normal' ? 'selected' : '' }}>{{ __('Physical Product') }}</option>
                            <option value="digital" {{ request('item_type') == 'digital' ? 'selected' : '' }}>{{ __('Digital Product') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                        <select class="form-control" name="category_id">
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach(DB::table('categories')->whereStatus(1)->get() as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                        <select class="form-control" name="orderby">
                            <option value="desc" {{ request('orderby') == 'desc' ? 'selected' : '' }}>{{ __('Newest First') }}</option>
                            <option value="asc" {{ request('orderby') == 'asc' ? 'selected' : '' }}>{{ __('Oldest First') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                        <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-filter mr-1"></i> {{ __('Filter Products') }}</button>
                    </div>
                </div>
            </form>

            <hr>

            <div class="gd-responsive-table mt-3">
                <table class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ __('Image') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Stock') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Type') }}</th>
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
                                    @if($data->approval_status == 'Rejected' && $data->reject_reason)
                                        <div class="alert alert-danger py-2 px-3 mt-2 mb-0 small" style="border-left: 4px solid #dc3545;">
                                            <strong><i class="fas fa-exclamation-triangle mr-1"></i> {{ __('Admin Rejection Note:') }}</strong>
                                            <p class="mb-1 text-dark">{{ $data->reject_reason }}</p>
                                            <a href="{{ route('seller.item.edit', $data->id) }}" class="btn btn-danger btn-xs py-1 px-2 font-weight-bold text-white">
                                                <i class="fas fa-edit mr-1"></i> {{ __('Edit & Resubmit for Approval') }}
                                            </a>
                                        </div>
                                    @elseif($data->approval_status == 'Pending')
                                        <div class="badge badge-light border text-warning mt-1 small">
                                            <i class="fas fa-hourglass-half mr-1"></i> {{ __('Awaiting admin verification') }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ PriceHelper::setCurrencyPrice($data->discount_price) }}</strong>
                                    @if($data->previous_price > 0)
                                        <del class="small text-muted d-block">{{ PriceHelper::setCurrencyPrice($data->previous_price) }}</del>
                                    @endif
                                </td>
                                <td>
                                    @if($data->item_type == 'normal')
                                        @if($data->stock > 0)
                                            <span class="badge badge-success">{{ $data->stock }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ __('Out of Stock') }}</span>
                                        @endif
                                    @else
                                        <span class="badge badge-info">{{ __('Unlimited') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($data->approval_status == 'Pending')
                                        <span class="badge badge-warning text-dark font-weight-bold py-1 px-2">
                                            <i class="fas fa-clock mr-1"></i> {{ __('Pending Review') }}
                                        </span>
                                        <small class="text-muted d-block mt-1 font-italic">{{ __('Hidden') }}</small>
                                    @elseif($data->approval_status == 'Rejected')
                                        <span class="badge badge-danger font-weight-bold py-1 px-2">
                                            <i class="fas fa-times-circle mr-1"></i> {{ __('Rejected') }}
                                        </span>
                                        <small class="text-danger d-block mt-1 font-weight-bold">{{ __('Action Needed') }}</small>
                                    @elseif($data->approval_status == 'Approved')
                                        @if($data->status == 1)
                                            <a href="{{ route('seller.item.status', [$data->id, 0]) }}" class="badge badge-success" title="{{ __('Click to disable') }}"><i class="fas fa-check mr-1"></i> {{ __('Live (Active)') }}</a>
                                        @else
                                            <a href="{{ route('seller.item.status', [$data->id, 1]) }}" class="badge badge-secondary" title="{{ __('Click to activate') }}"><i class="fas fa-pause mr-1"></i> {{ __('Paused') }}</a>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-primary text-uppercase">{{ $data->item_type }}</span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('seller.item.edit', $data->id) }}" class="btn btn-info btn-sm" title="{{ __('Edit Product') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('seller.item.gallery', $data->id) }}" class="btn btn-warning btn-sm" title="{{ __('Galleries') }}">
                                            <i class="fas fa-images"></i>
                                        </a>
                                        <a href="{{ route('front.product', $data->slug) }}" target="_blank" class="btn btn-secondary btn-sm" title="{{ __('View on Store') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" title="{{ __('Delete') }}" onclick="confirmDelete('{{ route('seller.item.destroy', $data->id) }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fab fa-product-hunt fa-3x mb-2 d-block"></i>
                                    {{ __('No products found in your store. Click "Add Product" above to create your first listing!') }}
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

<!-- DELETE MODAL -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white"><i class="fas fa-trash mr-2"></i> {{ __('Delete Product') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4 text-center">
                    <p class="text-dark">{{ __('Are you sure you want to delete this product? This action cannot be undone.') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Delete Product') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmDelete(url) {
        $('#deleteForm').attr('action', url);
        $('#deleteModal').modal('show');
    }
</script>
@endsection
