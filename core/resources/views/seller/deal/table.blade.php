<style>
    .seller-deal-table td, .seller-deal-table th { vertical-align: middle; white-space: nowrap; font-size: 13px; }
    .seller-deal-name { max-width: 160px; white-space: normal; word-break: break-word; }
</style>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-sm seller-deal-table">
        <thead class="thead-light">
            <tr>
                <th>{{ __('Bundle Name') }}</th>
                <th>{{ __('Items') }}</th>
                <th>{{ __('Original') }}</th>
                <th>{{ __('Deal Price') }}</th>
                <th>{{ __('Discount') }}</th>
                <th>{{ __('Time Left') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $deal)
                <tr>
                    <td class="seller-deal-name"><strong>{{ $deal->name }}</strong></td>
                    <td><span class="badge badge-secondary">{{ $deal->dealItems->count() }}</span></td>
                    <td><del class="text-muted">{{ PriceHelper::setCurrencyPrice($deal->original_price) }}</del></td>
                    <td><strong class="text-success">{{ PriceHelper::setCurrencyPrice($deal->discounted_price) }}</strong></td>
                    <td><span class="badge badge-warning text-dark">{{ $deal->discount_badge }}</span></td>
                    <td>
                        @if($deal->end_date && !$deal->isExpired())
                            <small class="text-danger font-weight-bold">{{ $deal->end_date->diffForHumans(['parts' => 2]) }}</small>
                        @elseif($deal->end_date)
                            <small class="text-muted">{{ $deal->end_date->format('M d, Y') }}</small>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <div class="d-flex" style="gap:4px">
                            <button class="btn btn-info btn-sm" onclick="sellerViewDeal({{ $deal->id }})" title="{{ __('View') }}"><i class="fas fa-eye"></i></button>
                            <a class="btn btn-secondary btn-sm" href="{{ route('seller.deal.edit', $deal->id) }}" title="{{ __('Edit') }}"><i class="fas fa-edit"></i></a>
                            <form class="d-inline" method="post" action="{{ route('seller.deal.destroy', $deal->id) }}" onsubmit="return confirm('{{ __('Delete this bundle?') }}')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" title="{{ __('Delete') }}"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">{{ __('No bundles found.') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@php
$sellerDealsData = [];
foreach($rows as $d) {
    $sellerDealsData[$d->id] = [
        'name'        => $d->name,
        'description' => $d->description,
        'original'    => PriceHelper::setCurrencyPrice($d->original_price),
        'price'       => PriceHelper::setCurrencyPrice($d->discounted_price),
        'discount'    => $d->discount_badge,
        'delivery'    => $d->is_free_delivery ? 'Free' : PriceHelper::setCurrencyPrice($d->delivery_charge),
        'orders'      => $d->orders_count,
        'start'       => $d->start_date ? $d->start_date->format('M d, Y h:i A') : '-',
        'end'         => $d->end_date ? $d->end_date->format('M d, Y h:i A') : '-',
        'timeleft'    => $d->end_date && !$d->isExpired() ? $d->end_date->diffForHumans(['parts' => 2]) : null,
        'expired'     => $d->isExpired(),
        'items'       => $d->dealItems->map(fn($di) => ['name' => optional($di->item)->name ?? 'Product', 'original' => PriceHelper::setCurrencyPrice($di->original_price), 'price' => PriceHelper::setCurrencyPrice($di->discounted_price)])->toArray(),
    ];
}
@endphp
<script>
var sellerDealsData = Object.assign(typeof sellerDealsData !== 'undefined' ? sellerDealsData : {}, @json($sellerDealsData));
function sellerViewDeal(id) {
    var d = sellerDealsData[id];
    if (!d) return;
    var itemRows = '';
    d.items.forEach(function(item) {
        itemRows += '<tr><td>' + item.name + '</td><td><del class="text-muted">' + item.original + '</del></td><td><strong class="text-success">' + item.price + '</strong></td></tr>';
    });
    var html = '<div class="row">'
        + '<div class="col-md-6">'
        + '<table class="table table-sm table-borderless mb-0">'
        + '<tr><th class="text-muted" style="width:130px">Bundle Name</th><td><strong>' + d.name + '</strong></td></tr>'
        + (d.description ? '<tr><th class="text-muted">Description</th><td>' + d.description + '</td></tr>' : '')
        + '<tr><th class="text-muted">Original Price</th><td><del>' + d.original + '</del></td></tr>'
        + '<tr><th class="text-muted">Deal Price</th><td><strong class="text-success">' + d.price + '</strong></td></tr>'
        + '<tr><th class="text-muted">Discount</th><td><span class="badge badge-warning text-dark">' + d.discount + '</span></td></tr>'
        + '<tr><th class="text-muted">Delivery</th><td>' + d.delivery + '</td></tr>'
        + '<tr><th class="text-muted">Orders Placed</th><td><span class="badge badge-dark">' + d.orders + '</span></td></tr>'
        + '<tr><th class="text-muted">Start Date</th><td>' + d.start + '</td></tr>'
        + '<tr><th class="text-muted">End Date</th><td>' + d.end + '</td></tr>'
        + (d.timeleft ? '<tr><th class="text-muted">Time Left</th><td><span class="text-danger font-weight-bold">' + d.timeleft + '</span></td></tr>' : '')
        + '<tr><th class="text-muted">Status</th><td><span class="badge badge-' + (d.expired ? 'danger' : 'success') + '">' + (d.expired ? 'Expired' : 'Active') + '</span></td></tr>'
        + '</table>'
        + '</div>'
        + '<div class="col-md-6 mt-3 mt-md-0">'
        + '<h6 class="font-weight-bold mb-2">Included Products (' + d.items.length + ')</h6>'
        + '<div class="table-responsive"><table class="table table-sm table-bordered mb-0"><thead class="thead-light"><tr><th>Product</th><th>Original</th><th>Deal Price</th></tr></thead><tbody>' + itemRows + '</tbody></table></div>'
        + '</div>'
        + '</div>';
    $('#seller-deal-view-body').html(html);
    $('#seller-deal-view-modal').modal('show');
}
</script>
