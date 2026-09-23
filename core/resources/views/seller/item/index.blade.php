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

            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped table-hover align-middle" style="width: 100%; min-width: 820px;" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th style="min-width: 260px; width: 36%;">{{ __('Product') }}</th>
                            <th style="min-width: 130px; width: 15%;">{{ __('Price') }}</th>
                            <th style="min-width: 140px; width: 17%;">{{ __('Stock & Status') }}</th>
                            <th style="min-width: 150px; width: 16%;">{{ __('Category') }}</th>
                            <th style="min-width: 140px; width: 16%; text-align: center;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($datas as $data)
                            <tr>
                                <td style="vertical-align: middle;">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-2 flex-shrink-0">
                                            @php
                                                $imgSrc = $data->thumbnail 
                                                    ? url('/core/public/storage/images/'.$data->thumbnail) 
                                                    : ($data->photo ? url('/core/public/storage/images/'.$data->photo) : url('/core/public/storage/images/placeholder.png'));
                                            @endphp
                                            <img src="{{ $imgSrc }}" alt="{{ $data->name }}" style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;">
                                        </div>
                                        <div>
                                            <strong class="text-dark d-block" style="font-size: 13.5px; line-height: 1.3;" title="{{ $data->name }}">
                                                {{ \Illuminate\Support\Str::limit($data->name, 50) }}
                                            </strong>
                                            <div class="small text-muted mt-1">
                                                @if($data->sku)
                                                    <span class="mr-1"><code>{{ $data->sku }}</code></span>
                                                @endif
                                                <span class="badge badge-light border text-muted" style="font-size: 10.5px;">{{ ucfirst($data->item_type) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="vertical-align: middle;">
                                    <strong class="text-dark font-weight-bold" style="font-size: 14px;">{{ PriceHelper::setCurrencyPrice($data->discount_price) }}</strong>
                                    @if($data->previous_price > 0)
                                        <del class="small text-muted d-block font-italic">{{ PriceHelper::setCurrencyPrice($data->previous_price) }}</del>
                                    @endif
                                </td>
                                <td style="vertical-align: middle;">
                                    <div class="mb-1">
                                        @if($data->item_type == 'normal')
                                            @if($data->stock > 0)
                                                <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 11px;">
                                                    <i class="fas fa-cubes mr-1"></i> {{ $data->stock }} {{ __('in stock') }}
                                                </span>
                                            @else
                                                <span class="badge badge-danger px-2 py-1 font-weight-bold" style="font-size: 11px;">
                                                    <i class="fas fa-times-circle mr-1"></i> {{ __('Out of Stock') }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 11px;">
                                                <i class="fas fa-infinity mr-1"></i> {{ __('Unlimited') }}
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        @if($data->approval_status == 'Pending')
                                            <span class="badge badge-warning text-dark px-2 py-1" style="font-size: 11px;"><i class="fas fa-clock mr-1"></i> {{ __('Pending') }}</span>
                                        @elseif($data->approval_status == 'Rejected')
                                            <span class="badge badge-danger px-2 py-1" style="font-size: 11px;"><i class="fas fa-times-circle mr-1"></i> {{ __('Rejected') }}</span>
                                        @elseif($data->approval_status == 'Approved')
                                            @if($data->status == 1)
                                                <a href="{{ route('seller.item.status', [$data->id, 0]) }}" class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 11px;" title="{{ __('Click to Pause') }}">
                                                    <i class="fas fa-check-circle mr-1"></i> {{ __('Live') }}
                                                </a>
                                            @else
                                                <a href="{{ route('seller.item.status', [$data->id, 1]) }}" class="badge badge-secondary px-2 py-1 font-weight-bold" style="font-size: 11px;" title="{{ __('Click to Activate') }}">
                                                    <i class="fas fa-pause-circle mr-1"></i> {{ __('Paused') }}
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td style="vertical-align: middle;">
                                    <span class="badge badge-primary font-weight-bold d-inline-block text-wrap" style="font-size: 11.5px; padding: 5px 8px; border-radius: 6px; line-height: 1.3;">
                                        <i class="fas fa-folder mr-1"></i> {{ $data->category ? $data->category->name : '-' }}
                                    </span>
                                    @if($data->subcategory)
                                        <div class="mt-1">
                                            <span class="badge badge-light border text-muted d-inline-block text-wrap" style="font-size: 11px; padding: 3px 6px;">
                                                <i class="fas fa-angle-right mr-1 text-primary"></i> {{ $data->subcategory->name }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <div class="d-flex justify-content-center align-items-center" style="gap: 4px;">
                                        <a href="{{ route('seller.item.edit', $data->id) }}" class="btn btn-primary btn-sm px-2 py-1" title="{{ __('Edit') }}" style="min-width: 32px;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('seller.item.gallery', $data->id) }}" class="btn btn-warning btn-sm px-2 py-1 text-dark" title="{{ __('Galleries') }}" style="min-width: 32px;">
                                            <i class="fas fa-images"></i>
                                        </a>
                                        <a href="{{ route('front.product', $data->slug) }}" target="_blank" class="btn btn-secondary btn-sm px-2 py-1" title="{{ __('View') }}" style="min-width: 32px;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm px-2 py-1" onclick="confirmDelete('{{ route('seller.item.destroy', $data->id) }}')" title="{{ __('Delete') }}" style="min-width: 32px;">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fab fa-product-hunt fa-3x mb-2 d-block text-muted opacity-50"></i>
                                    <p class="mb-0 font-weight-bold">{{ __('No products found.') }}</p>
                                    <small>{{ __('Click "Add Product" above to create your first listing!') }}</small>
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
