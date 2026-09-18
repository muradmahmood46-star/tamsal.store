@extends('master.back')

@section('styles')
<style>
    #admin-deal-active-datatable td,
    #admin-deal-expired-datatable td,
    #admin-deal-active-datatable th,
    #admin-deal-expired-datatable th {
        vertical-align: middle;
        white-space: nowrap;
        font-size: 13px;
    }
    .deal-name-cell { max-width: 180px; white-space: normal; word-break: break-word; }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 bc-title"><b>{{ __('Manage Bundles') }}</b></h3>
                <a class="btn btn-primary btn-sm" href="{{ route('back.deal.create') }}"><i class="fas fa-plus"></i> {{ __('Create New Bundle') }}</a>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            @include('alerts.alerts')

            <ul class="nav nav-pills nav-secondary mb-4" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pills-active-tab" data-toggle="pill" href="#pills-active" role="tab">{{ __('Active Bundles') }} ({{ $deals->where('status', 1)->filter(fn($d) => !$d->isExpired())->count() }})</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-expired-tab" data-toggle="pill" href="#pills-expired" role="tab">{{ __('Past / Expired') }} ({{ $deals->filter(fn($d) => $d->isExpired())->count() }})</a>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">

                {{-- Active --}}
                <div class="tab-pane fade show active" id="pills-active" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm" id="admin-deal-active-datatable" width="100%">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('Bundle Name') }}</th>
                                    <th>{{ __('Creator') }}</th>
                                    <th>{{ __('Items') }}</th>
                                    <th>{{ __('Original') }}</th>
                                    <th>{{ __('Deal Price') }}</th>
                                    <th>{{ __('Discount') }}</th>
                                    <th>{{ __('Time Left') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deals->where('status', 1)->filter(fn($d) => !$d->isExpired()) as $data)
                                    <tr>
                                        <td class="deal-name-cell"><strong>{{ $data->name }}</strong></td>
                                        <td>
                                            @if($data->vendor_id > 0 && $data->vendor)
                                                <span class="badge badge-info">{{ __('Vendor') }}</span>
                                            @else
                                                <span class="badge badge-primary">{{ __('Admin') }}</span>
                                            @endif
                                        </td>
                                        <td><span class="badge badge-secondary">{{ $data->dealItems->count() }}</span></td>
                                        <td><del class="text-muted">{{ PriceHelper::setCurrencyPrice($data->original_price) }}</del></td>
                                        <td><strong class="text-success">{{ PriceHelper::setCurrencyPrice($data->discounted_price) }}</strong></td>
                                        <td><span class="badge badge-warning text-dark">{{ $data->discount_badge }}</span></td>
                                        <td>
                                            @if($data->end_date)
                                                <small class="text-danger font-weight-bold">{{ $data->end_date->diffForHumans(['parts' => 2]) }}</small>
                                            @else
                                                -
                                            @endif
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
                                            <div class="d-flex" style="gap:4px">
                                                <button class="btn btn-info btn-sm" onclick="viewDeal({{ $data->id }})" title="{{ __('View') }}"><i class="fas fa-eye"></i></button>
                                                <a class="btn btn-secondary btn-sm" href="{{ route('back.deal.edit', $data->id) }}" title="{{ __('Edit') }}"><i class="fas fa-edit"></i></a>
                                                <a class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" href="javascript:;" data-href="{{ route('back.deal.destroy', $data->id) }}" title="{{ __('Delete') }}"><i class="fas fa-trash-alt"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Expired --}}
                <div class="tab-pane fade" id="pills-expired" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm" id="admin-deal-expired-datatable" width="100%">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('Bundle Name') }}</th>
                                    <th>{{ __('Creator') }}</th>
                                    <th>{{ __('Items') }}</th>
                                    <th>{{ __('Original') }}</th>
                                    <th>{{ __('Deal Price') }}</th>
                                    <th>{{ __('Discount') }}</th>
                                    <th>{{ __('Expired') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deals->filter(fn($d) => $d->isExpired()) as $data)
                                    <tr>
                                        <td class="deal-name-cell"><strong>{{ $data->name }}</strong></td>
                                        <td>
                                            @if($data->vendor_id > 0 && $data->vendor)
                                                <span class="badge badge-info">{{ __('Vendor') }}</span>
                                            @else
                                                <span class="badge badge-primary">{{ __('Admin') }}</span>
                                            @endif
                                        </td>
                                        <td><span class="badge badge-secondary">{{ $data->dealItems->count() }}</span></td>
                                        <td><del class="text-muted">{{ PriceHelper::setCurrencyPrice($data->original_price) }}</del></td>
                                        <td><strong class="text-muted">{{ PriceHelper::setCurrencyPrice($data->discounted_price) }}</strong></td>
                                        <td><span class="badge badge-secondary">{{ $data->discount_badge }}</span></td>
                                        <td><small class="text-danger">{{ $data->end_date ? $data->end_date->format('M d, Y') : '-' }}</small></td>
                                        <td><span class="badge badge-danger">{{ __('Expired') }}</span></td>
                                        <td>
                                            <div class="d-flex" style="gap:4px">
                                                <button class="btn btn-info btn-sm" onclick="viewDeal({{ $data->id }})" title="{{ __('View') }}"><i class="fas fa-eye"></i></button>
                                                <a class="btn btn-secondary btn-sm" href="{{ route('back.deal.edit', $data->id) }}" title="{{ __('Edit') }}"><i class="fas fa-edit"></i></a>
                                                <a class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" href="javascript:;" data-href="{{ route('back.deal.destroy', $data->id) }}" title="{{ __('Delete') }}"><i class="fas fa-trash-alt"></i></a>
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

{{-- View Detail Modal --}}
<div class="modal fade" id="deal-view-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-box-open text-primary mr-2"></i> {{ __('Bundle Details') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="deal-view-body">
                <div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Modal --}}
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

{{-- Inline deal data for modal --}}
@php
$dealsData = [];
foreach($deals as $d) {
    $dealsData[$d->id] = [
        'name'        => $d->name,
        'description' => $d->description,
        'creator'     => $d->vendor_id > 0 && $d->vendor ? ($d->vendor->first_name.' '.$d->vendor->last_name.' (Vendor)') : 'Admin',
        'original'    => PriceHelper::setCurrencyPrice($d->original_price),
        'price'       => PriceHelper::setCurrencyPrice($d->discounted_price),
        'discount'    => $d->discount_badge,
        'delivery'    => $d->is_free_delivery ? 'Free' : PriceHelper::setCurrencyPrice($d->delivery_charge),
        'orders'      => $d->orders_count,
        'start'       => $d->start_date ? $d->start_date->format('M d, Y h:i A') : '-',
        'end'         => $d->end_date ? $d->end_date->format('M d, Y h:i A') : '-',
        'timeleft'    => $d->end_date ? $d->end_date->diffForHumans(['parts' => 2]) : '-',
        'expired'     => $d->isExpired(),
        'store_url'   => route('front.deal.details', $d->slug),
        'items'       => $d->dealItems->map(fn($di) => ['name' => $di->item->name ?? 'Product', 'original' => PriceHelper::setCurrencyPrice($di->original_price), 'price' => PriceHelper::setCurrencyPrice($di->discounted_price)])->toArray(),
    ];
}
@endphp

@endsection

@section('scripts')
<script>
var dealsData = @json($dealsData);

function viewDeal(id) {
    var d = dealsData[id];
    if (!d) return;
    var itemRows = '';
    d.items.forEach(function(item) {
        itemRows += '<tr><td>' + item.name + '</td><td><del class="text-muted">' + item.original + '</del></td><td><strong class="text-success">' + item.price + '</strong></td></tr>';
    });
    var html = '<div class="row">'
        + '<div class="col-md-6">'
        + '<table class="table table-sm table-borderless mb-0">'
        + '<tr><th class="text-muted" style="width:130px">{{ __("Bundle Name") }}</th><td><strong>' + d.name + '</strong></td></tr>'
        + (d.description ? '<tr><th class="text-muted">{{ __("Description") }}</th><td>' + d.description + '</td></tr>' : '')
        + '<tr><th class="text-muted">{{ __("Creator") }}</th><td>' + d.creator + '</td></tr>'
        + '<tr><th class="text-muted">{{ __("Original Price") }}</th><td><del>' + d.original + '</del></td></tr>'
        + '<tr><th class="text-muted">{{ __("Deal Price") }}</th><td><strong class="text-success">' + d.price + '</strong></td></tr>'
        + '<tr><th class="text-muted">{{ __("Discount") }}</th><td><span class="badge badge-warning text-dark">' + d.discount + '</span></td></tr>'
        + '<tr><th class="text-muted">{{ __("Delivery") }}</th><td>' + d.delivery + '</td></tr>'
        + '<tr><th class="text-muted">{{ __("Orders Placed") }}</th><td><span class="badge badge-dark">' + d.orders + '</span></td></tr>'
        + '<tr><th class="text-muted">{{ __("Start Date") }}</th><td>' + d.start + '</td></tr>'
        + '<tr><th class="text-muted">{{ __("End Date") }}</th><td>' + d.end + '</td></tr>'
        + (!d.expired ? '<tr><th class="text-muted">{{ __("Time Left") }}</th><td><span class="text-danger font-weight-bold">' + d.timeleft + '</span></td></tr>' : '')
        + '<tr><th class="text-muted">{{ __("Status") }}</th><td><span class="badge badge-' + (d.expired ? 'danger' : 'success') + '">' + (d.expired ? '{{ __("Expired") }}' : '{{ __("Active") }}') + '</span></td></tr>'
        + '</table>'
        + (!d.expired ? '<a href="' + d.store_url + '" target="_blank" class="btn btn-outline-primary btn-sm mt-2"><i class="fas fa-external-link-alt"></i> {{ __("View on Store") }}</a>' : '')
        + '</div>'
        + '<div class="col-md-6 mt-3 mt-md-0">'
        + '<h6 class="font-weight-bold mb-2">{{ __("Included Products") }} (' + d.items.length + ')</h6>'
        + '<div class="table-responsive"><table class="table table-sm table-bordered mb-0"><thead class="thead-light"><tr><th>{{ __("Product") }}</th><th>{{ __("Original") }}</th><th>{{ __("Deal Price") }}</th></tr></thead><tbody>' + itemRows + '</tbody></table></div>'
        + '</div>'
        + '</div>';
    $('#deal-view-body').html(html);
    $('#deal-view-modal').modal('show');
}

$(document).ready(function() {
    $('#confirm-delete').on('show.bs.modal', function(e) {
        $(this).find('.btn-ok').attr('action', $(e.relatedTarget).data('href'));
    });
});
</script>
@endsection
