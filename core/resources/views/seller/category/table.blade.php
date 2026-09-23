@foreach($datas as $data)
@php
    $isOwner = ($data->vendor_id == Auth::id());
@endphp
<tr>
    <td class="text-center" style="vertical-align: middle; width: 80px;">
        <img src="{{ $data->photo ? url('/core/public/storage/images/'.$data->photo) : url('/core/public/storage/images/placeholder.png') }}" alt="Image Not Found" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;">
    </td>
    <td style="vertical-align: middle;">
        <div class="d-flex align-items-center flex-wrap" style="gap: 5px;">
            <strong class="text-dark" style="font-size: 14px;">{{ $data->name }}</strong>
            @if($isOwner)
                <span class="badge badge-info" style="font-size: 10.5px; padding: 3px 6px;">{{ __('My Category') }}</span>
            @else
                <span class="badge badge-secondary" style="font-size: 10.5px; padding: 3px 6px;"><i class="fas fa-shield-alt"></i> {{ __('Admin') }}</span>
            @endif
        </div>
        <div class="small text-muted font-italic mt-1" style="word-break: break-all;">{{ $data->slug }}</div>
    </td>

    <td class="text-center" style="vertical-align: middle;">
        @if($isOwner)
            <div class="dropdown">
                <button class="btn btn-{{ $data->status == 1 ? 'success' : 'danger' }} btn-sm dropdown-toggle font-weight-bold px-2 py-1" type="button" id="dropdownMenuButton_{{ $data->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: 12px;">
                  {{ $data->status == 1 ? __('Enabled') : __('Disabled') }}
                </button>
                <div class="dropdown-menu animated--fade-in dropdown-menu-right" aria-labelledby="dropdownMenuButton_{{ $data->id }}">
                  <a class="dropdown-item text-success font-weight-bold" href="{{ route('seller.category.status', [$data->id, 1]) }}"><i class="fas fa-check mr-1"></i> {{ __('Enable') }}</a>
                  <a class="dropdown-item text-danger font-weight-bold" href="{{ route('seller.category.status', [$data->id, 0]) }}"><i class="fas fa-times mr-1"></i> {{ __('Disable') }}</a>
                </div>
            </div>
        @else
            <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 12px;">{{ __('Active') }}</span>
        @endif
    </td>
    <td class="text-center" style="vertical-align: middle;">
        @if($isOwner)
            <div class="d-flex justify-content-center align-items-center" style="gap: 5px;">
                <a class="btn btn-primary btn-sm px-2 py-1" href="{{ route('seller.category.edit', $data->id) }}" title="{{ __('Edit') }}">
                    <i class="fas fa-edit"></i>
                </a>
                <a class="btn btn-danger btn-sm px-2 py-1" data-toggle="modal" data-target="#confirm-delete" href="javascript:;" data-href="{{ route('seller.category.destroy', $data->id) }}" title="{{ __('Delete') }}">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </div>
        @else
            <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 11px;"><i class="fas fa-lock mr-1"></i>{{ __('Default') }}</span>
        @endif
    </td>
</tr>
@endforeach
