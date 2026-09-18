@foreach($datas as $data)
@php
    $isOwner = ($data->vendor_id == Auth::id());
@endphp
<tr>
    <td>
        <strong>{{ $data->name }}</strong>
        @if($isOwner)
            <span class="badge badge-info ml-1" style="font-size: 11px;">{{ __('My Brand') }}</span>
        @else
            <span class="badge badge-secondary ml-1" style="font-size: 11px;"><i class="fas fa-shield-alt"></i> {{ __('Admin') }}</span>
        @endif
    </td>
    <td>
        <img style="max-height: 40px; max-width: 80px; object-fit: contain;" src="{{ $data->photo ? url('/core/public/storage/images/'.$data->photo) : url('/core/public/storage/images/placeholder.png') }}" alt="{{ $data->name }}">
    </td>
    <td>
        <code>{{ $data->slug }}</code>
    </td>
    <td>
        @if($isOwner)
            <div class="dropdown">
                <button class="btn btn-{{  $data->status == 1 ? 'success' : 'danger'  }} btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{  $data->status == 1 ? __('Enabled') : __('Disabled')  }}
                </button>
                <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="{{ route('seller.brand.status', [$data->id, 1, 'status']) }}">{{ __('Enable') }}</a>
                    <a class="dropdown-item" href="{{ route('seller.brand.status', [$data->id, 0, 'status']) }}">{{ __('Disable') }}</a>
                </div>
            </div>
        @else
            <span class="badge badge-success px-2 py-1">{{ __('Active') }}</span>
        @endif
    </td>
    <td>
        @if($isOwner)
            <div class="action-list">
                <a class="btn btn-secondary btn-sm" href="{{ route('seller.brand.edit', $data->id) }}" title="{{ __('Edit') }}">
                    <i class="fas fa-edit"></i>
                </a>
                <a class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" href="javascript:;" data-href="{{ route('seller.brand.destroy', $data->id) }}" title="{{ __('Delete') }}">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </div>
        @else
            <span class="text-muted small font-weight-bold"><i class="fas fa-lock mr-1"></i>{{ __('System Default') }}</span>
        @endif
    </td>
</tr>
@endforeach
