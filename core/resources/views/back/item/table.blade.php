@foreach($datas as $data)
<tr id="product-bulk-delete" class="admin-product-row">
    <td class="text-center" style="vertical-align: middle; width: 30px; padding: 4px 6px;">
        <input type="checkbox" class="bulk-item" value="{{$data->id}}">
    </td>
    <td class="text-center" style="vertical-align: middle; width: 55px; padding: 4px 6px;">
        <div class="position-relative d-inline-block">
            <img class="admin-product-thumb" src="{{ $data->thumbnail ? url('/core/public/storage/images/'.$data->thumbnail) : url('/core/public/storage/images/placeholder.png') }}" alt="{{ $data->name }}" style="width: 44px; height: 44px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;">
            @if($data->galleries && $data->galleries->count() > 0)
                <a href="{{ route('back.item.gallery', $data->id) }}" class="badge badge-warning position-absolute shadow-sm gallery-count-badge" style="top: -5px; right: -6px; font-size: 9.5px; padding: 1px 4px; border-radius: 8px;" title="{{ __('Total Gallery Images') }}">
                    <i class="fas fa-images"></i> +{{ $data->galleries->count() }}
                </a>
            @endif
        </div>
    </td>
    <td class="admin-product-name-col" style="vertical-align: middle; padding: 4px 8px;">
        <div class="admin-product-name-text" title="{{ $data->name }}">
            <span class="d-none d-md-inline">{{ \Illuminate\Support\Str::limit($data->name, 55) }}</span>
            <span class="d-inline d-md-none text-truncate-mobile">{{ \Illuminate\Support\Str::limit($data->name, 22) }}</span>
        </div>
        @if($data->sku)
            <small class="text-muted d-block text-nowrap mt-1" style="font-size: 11px;"><code>{{ $data->sku }}</code></small>
        @endif
    </td>
    <td style="vertical-align: middle; white-space: nowrap; font-weight: 600; font-size: 13px; padding: 4px 8px;">
        {{ PriceHelper::adminCurrencyPrice($data->discount_price) }}
    </td>
    <td style="vertical-align: middle; white-space: nowrap; padding: 4px 6px;">
        <div class="dropdown">
            <button class="btn btn-{{  $data->status == 1 ? 'success' : 'danger'  }} btn-sm dropdown-toggle py-1 px-2" type="button" id="dropdownMenuButton_{{$data->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: 11px;">
              {{  $data->status == 1 ? __('Publish') : __('Unpublish')  }}
            </button>
            <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton_{{$data->id}}">
              <a class="dropdown-item" href="{{ route('back.item.status',[$data->id,1]) }}">{{ __('Publish') }}</a>
              <a class="dropdown-item" href="{{ route('back.item.status',[$data->id,0]) }}">{{ __('Unpublish') }}</a>
            </div>
        </div>
    </td>
    <td style="vertical-align: middle; white-space: nowrap; padding: 4px 6px;">
      @if($data->isPromotionActive())
        <span class="badge badge-warning text-dark font-weight-bold" style="background:#fef08a; border:1px solid #facc15; font-size: 10.5px;"><i class="fas fa-crown text-warning mr-1"></i>{{ $data->promotion_tag }}</span>
      @elseif($data->is_type && $data->is_type != 'undefine')
        <span class="badge badge-info text-white" style="font-size: 10.5px;">{{ ucfirst(str_replace('_',' ',$data->is_type)) }}</span>
      @else
        <span class="badge badge-secondary" style="font-size: 10.5px;">{{ __('Not Define') }}</span>
      @endif
    </td>
    <td style="vertical-align: middle; white-space: nowrap; font-size: 11.5px; padding: 4px 6px;">
      <span class="badge badge-light border text-muted px-2 py-1">{{ ucfirst($data->item_type) }}</span>
    </td>
    <td style="vertical-align: middle; white-space: nowrap; padding: 4px 6px;">
        <div class="dropdown">
            <button class="btn btn-secondary btn-sm dropdown-toggle py-1 px-2" type="button" id="dropdownOption_{{$data->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: 11px;">
              {{  __('Options') }}
            </button>
            <div class="dropdown-menu dropdown-menu-right animated--fade-in" aria-labelledby="dropdownOption_{{$data->id}}">
              @if ($data->item_type == 'normal')
              <a class="dropdown-item" href="{{ route('back.item.edit',$data->id) }}"><i class="fas fa-angle-double-right"></i> {{ __('Edit') }}</a>
              @elseif($data->item_type =='digital')
              <a class="dropdown-item" href="{{ route('back.digital.item.edit',$data->id) }}"><i class="fas fa-angle-double-right"></i> {{ __('Edit') }}</a>
              @elseif($data->item_type =='affiliate')
              <a class="dropdown-item" href="{{ route('back.affiliate.edit',$data->id) }}"><i class="fas fa-angle-double-right"></i> {{ __('Edit') }}</a>
              @else
              <a class="dropdown-item" href="{{ route('back.license.item.edit',$data->id) }}"><i class="fas fa-angle-double-right"></i> {{ __('Edit') }}</a>
              @endif
              <a class="dropdown-item" href="{{ route('back.item.gallery', $data->id) }}"><i class="fas fa-angle-double-right"></i> {{ __('Galleries') }} ({{ $data->galleries ? $data->galleries->count() : 0 }})</a>
              @if($data->status == 1)
                <a class="dropdown-item" target="_blank" href="{{ route('front.product',$data->slug) }}"><i class="fas fa-angle-double-right"></i> {{ __('View') }}</a>
              @endif
              @if ($data->item_type == 'normal')
              <a class="dropdown-item" href="{{ route('back.attribute.index',$data->id) }}"><i class="fas fa-angle-double-right"></i> {{ __('Attributes') }}</a>
              <a class="dropdown-item" href="{{ route('back.option.index',$data->id) }}"><i class="fas fa-angle-double-right"></i> {{ __('Attribute Options') }}</a>
              @endif
              <a class="dropdown-item" href="{{ route('back.item.highlight',$data->id) }}"><i class="fas fa-angle-double-right"></i> {{ __('Highlight') }}</a>
              <a class="dropdown-item" data-toggle="modal"
              data-target="#confirm-delete" href="javascript:;"
              data-href="{{ route('back.item.destroy',$data->id) }}"><i class="fas fa-angle-double-right"></i> {{ __('Delete') }}</a>
            </div>
        </div>
    </td>
</tr>
@endforeach
