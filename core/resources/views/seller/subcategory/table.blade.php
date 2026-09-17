@foreach($datas as $data)
<tr>
    <td>
        <span class="badge badge-primary font-weight-bold" style="font-size: 13px;">{{ $data->category ? $data->category->name : __('No Category') }}</span>
    </td>
    <td>
        <strong>{{ $data->name }}</strong>
        <div class="small text-muted">{{ $data->slug }}</div>
    </td>

    <td>
        <div class="dropdown">
            <button class="btn btn-{{ $data->status == 1 ? 'success' : 'danger' }} btn-sm dropdown-toggle" type="button" id="dropdownMenuButton_{{ $data->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              {{ $data->status == 1 ? __('Enabled') : __('Disabled') }}
            </button>
            <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton_{{ $data->id }}">
              <a class="dropdown-item" href="{{ route('seller.subcategory.status', [$data->id, 1]) }}">{{ __('Enable') }}</a>
              <a class="dropdown-item" href="{{ route('seller.subcategory.status', [$data->id, 0]) }}">{{ __('Disable') }}</a>
            </div>
        </div>
    </td>
    <td>
        <div class="action-list">
            <a class="btn btn-secondary btn-sm" href="{{ route('seller.subcategory.edit', $data->id) }}" title="{{ __('Edit') }}">
                <i class="fas fa-edit"></i>
            </a>
            <a class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" href="javascript:;" data-href="{{ route('seller.subcategory.destroy', $data->id) }}" title="{{ __('Delete') }}">
                <i class="fas fa-trash-alt"></i>
            </a>
        </div>
    </td>
</tr>
@endforeach
