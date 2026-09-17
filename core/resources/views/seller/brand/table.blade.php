@foreach($datas as $data)
<tr>
    <td>
        <strong>{{ $data->name }}</strong>
    </td>
    <td>
        <img style="max-height: 40px; max-width: 80px; object-fit: contain;" src="{{ $data->photo ? url('/core/public/storage/images/'.$data->photo) : url('/core/public/storage/images/placeholder.png') }}" alt="{{ $data->name }}">
    </td>
    <td>
        <code>{{ $data->slug }}</code>
    </td>
    <td>
        <div class="dropdown">
            <button class="btn btn-{{  $data->status == 1 ? 'success' : 'danger'  }} btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{  $data->status == 1 ? __('Enabled') : __('Disabled')  }}
            </button>
            <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="{{ route('seller.brand.status', [$data->id, 1, 'status']) }}">{{ __('Enable') }}</a>
                <a class="dropdown-item" href="{{ route('seller.brand.status', [$data->id, 0, 'status']) }}">{{ __('Disable') }}</a>
            </div>
        </div>
    </td>
    <td>
        <div class="action-list">
            <a class="btn btn-secondary btn-sm" href="{{ route('seller.brand.edit', $data->id) }}" title="{{ __('Edit') }}">
                <i class="fas fa-edit"></i>
            </a>
            <a class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" href="javascript:;" data-href="{{ route('seller.brand.destroy', $data->id) }}" title="{{ __('Delete') }}">
                <i class="fas fa-trash-alt"></i>
            </a>
        </div>
    </td>
</tr>
@endforeach
