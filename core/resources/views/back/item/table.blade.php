@foreach($datas as $data)
<tr id="product-bulk-delete">
  <td><input type="checkbox" class="bulk-item" value="{{$data->id}}"></td>
    <td>
        <div class="position-relative d-inline-block">
            <img src="{{ $data->thumbnail ? url('/core/public/storage/images/'.$data->thumbnail) : url('/core/public/storage/images/placeholder.png') }}" alt="Image Not Found" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;">
            @if($data->galleries && $data->galleries->count() > 0)
                <a href="{{ route('back.item.gallery', $data->id) }}" class="badge badge-warning position-absolute shadow-sm" style="top: -6px; right: -8px; font-size: 10px; padding: 2px 5px; border-radius: 10px;" title="{{ __('Total Gallery Images') }}">
                    <i class="fas fa-images"></i> +{{ $data->galleries->count() }}
                </a>
            @endif
        </div>
    </td>
    <td>
        {{ $data->name }}
    </td>
    <td>
        {{ PriceHelper::adminCurrencyPrice($data->discount_price) }}
    </td>
    <td>
        <div class="dropdown">
            <button class="btn btn-{{  $data->status == 1 ? 'success' : 'danger'  }} btn-sm  dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              {{  $data->status == 1 ? __('Publish') : __('Unpublish')  }}
            </button>
            <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
              <a class="dropdown-item" href="{{ route('back.item.status',[$data->id,1]) }}">{{ __('Publish') }}</a>
              <a class="dropdown-item" href="{{ route('back.item.status',[$data->id,0]) }}">{{ __('Unpublish') }}</a>
            </div>
          </div>
    </td>
    <td>
      <p class="
        @if($data->is_type == 'undefine')
        @else
            bg-info badge text-white
        @endif
      ">
        @if($data->is_type == 'undefine')
            {{ __('Not Define') }}
        @else
            {{$data->is_type ? ucfirst(str_replace('_',' ',$data->is_type)) : __('undefine')}}
        @endif
        </p>
    </td>
    <td>
      {{ucfirst($data->item_type)}}
    </td>
    <td>
        <div class="dropdown">
            <button class="btn btn-secondary btn-sm  dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              {{  __('Options') }}
            </button>
            <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
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

        </div>
    </td>
</tr>
@endforeach
