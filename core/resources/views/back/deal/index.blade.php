@extends('master.back')

@section('content')

<!-- Start of Main Content -->
<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 bc-title"><b>{{ __('Manage Deals') }}</b></h3>
                <a class="btn btn-primary btn-sm" href="{{ route('back.deal.create') }}"><i class="fas fa-plus"></i> {{ __('Create New Deal') }}</a>
            </div>
        </div>
    </div>

	<!-- DataTales -->
	<div class="card shadow mb-4">
		<div class="card-body">
            @include('alerts.alerts')

            <ul class="nav nav-pills nav-secondary mb-4" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pills-active-tab" data-toggle="pill" href="#pills-active" role="tab" aria-controls="pills-active" aria-selected="true">{{ __('Active Deals') }} ({{ $deals->where('status', 1)->filter(fn($d) => !$d->isExpired())->count() }})</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-expired-tab" data-toggle="pill" href="#pills-expired" role="tab" aria-controls="pills-expired" aria-selected="false">{{ __('Past / Expired Deals') }} ({{ $deals->filter(fn($d) => $d->isExpired())->count() }})</a>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <!-- Active Deals Tab -->
                <div class="tab-pane fade show active" id="pills-active" role="tabpanel" aria-labelledby="pills-active-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="admin-deal-active-datatable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>{{ __('Deal Name') }}</th>
                                    <th>{{ __('Creator') }}</th>
                                    <th>{{ __('Products Included') }}</th>
                                    <th>{{ __('Original Price') }}</th>
                                    <th>{{ __('Deal Price') }}</th>
                                    <th>{{ __('Discount') }}</th>
                                    <th>{{ __('Duration / Time Left') }}</th>
                                    <th>{{ __('Orders Placed') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deals->where('status', 1)->filter(fn($d) => !$d->isExpired()) as $data)
                                    <tr>
                                        <td>
                                            <strong>{{ $data->name }}</strong>
                                            <div><small class="text-muted"><a href="{{ route('front.deal.details', $data->slug) }}" target="_blank"><i class="fas fa-external-link-alt"></i> {{ __('View on Store') }}</a></small></div>
                                        </td>
                                        <td>
                                            @if($data->vendor_id > 0 && $data->vendor)
                                                <span class="badge badge-info">{{ $data->vendor->first_name }} {{ $data->vendor->last_name }} ({{ __('Vendor') }})</span>
                                            @else
                                                <span class="badge badge-primary">{{ __('Admin') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary">{{ $data->dealItems->count() }} {{ __('Items') }}</span>
                                            <div class="mt-1">
                                                @foreach($data->dealItems->take(3) as $dItem)
                                                    <small class="d-block text-truncate" style="max-width: 150px;">• {{ $dItem->item->name ?? 'Product' }}</small>
                                                @endforeach
                                                @if($data->dealItems->count() > 3)
                                                    <small class="text-muted">+{{ $data->dealItems->count() - 3 }} {{ __('more') }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td><del>{{ PriceHelper::setCurrencyPrice($data->original_price) }}</del></td>
                                        <td><strong class="text-success">{{ PriceHelper::setCurrencyPrice($data->discounted_price) }}</strong></td>
                                        <td><span class="badge badge-warning text-dark font-weight-bold">{{ $data->discount_badge }}</span></td>
                                        <td>
                                            @if($data->end_date)
                                                <div><strong>{{ $data->end_date->format('M d, Y h:i A') }}</strong></div>
                                                <small class="text-danger font-weight-bold">
                                                    {{ $data->end_date->diffForHumans(['parts' => 2]) }}
                                                </small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-dark p-2" style="font-size: 13px;"><i class="fas fa-shopping-bag"></i> {{ $data->orders_count }}</span>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-{{ $data->status == 1 ? 'success' : 'danger' }} btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                                    {{ $data->status == 1 ? __('Active') : __('Inactive') }}
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="{{ route('back.deal.status', [$data->id, 1]) }}">{{ __('Active') }}</a>
                                                    <a class="dropdown-item" href="{{ route('back.deal.status', [$data->id, 0]) }}">{{ __('Inactive') }}</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="action-list">
                                                <a class="btn btn-secondary btn-sm" href="{{ route('back.deal.edit', $data->id) }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" href="javascript:;" data-href="{{ route('back.deal.destroy', $data->id) }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Expired Deals Tab -->
                <div class="tab-pane fade" id="pills-expired" role="tabpanel" aria-labelledby="pills-expired-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="admin-deal-expired-datatable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>{{ __('Deal Name') }}</th>
                                    <th>{{ __('Creator') }}</th>
                                    <th>{{ __('Products Included') }}</th>
                                    <th>{{ __('Original Price') }}</th>
                                    <th>{{ __('Deal Price') }}</th>
                                    <th>{{ __('Discount') }}</th>
                                    <th>{{ __('Expired Date') }}</th>
                                    <th>{{ __('Total Orders') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deals->filter(fn($d) => $d->isExpired()) as $data)
                                    <tr>
                                        <td>
                                            <strong>{{ $data->name }}</strong>
                                        </td>
                                        <td>
                                            @if($data->vendor_id > 0 && $data->vendor)
                                                <span class="badge badge-info">{{ $data->vendor->first_name }} {{ $data->vendor->last_name }} ({{ __('Vendor') }})</span>
                                            @else
                                                <span class="badge badge-primary">{{ __('Admin') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary">{{ $data->dealItems->count() }} {{ __('Items') }}</span>
                                        </td>
                                        <td><del>{{ PriceHelper::setCurrencyPrice($data->original_price) }}</del></td>
                                        <td><strong class="text-muted">{{ PriceHelper::setCurrencyPrice($data->discounted_price) }}</strong></td>
                                        <td><span class="badge badge-secondary">{{ $data->discount_badge }}</span></td>
                                        <td>
                                            <span class="text-danger font-weight-bold">{{ $data->end_date ? $data->end_date->format('M d, Y h:i A') : '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-dark p-2"><i class="fas fa-shopping-bag"></i> {{ $data->orders_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-danger">{{ __('Expired / Inactive') }}</span>
                                        </td>
                                        <td>
                                            <div class="action-list">
                                                <a class="btn btn-secondary btn-sm" href="{{ route('back.deal.edit', $data->id) }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" href="javascript:;" data-href="{{ route('back.deal.destroy', $data->id) }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

		</div>
	</div>

</div>

{{-- DELETE MODAL --}}
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('Confirm Delete') }}</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <p>{{ __('Are you sure you want to delete this deal? All deal items will also be removed.') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                <form action="" class="d-inline btn-ok" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#confirm-delete').on('show.bs.modal', function(e) {
            $(this).find('.btn-ok').attr('action', $(e.relatedTarget).data('href'));
        });
    });
</script>
@endsection
