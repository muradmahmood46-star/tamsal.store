@foreach($datas as $data)
<tr>
    <td style="vertical-align: middle;">
        <span class="badge badge-primary font-weight-bold d-inline-block text-wrap" style="font-size: 12px; padding: 6px 10px; border-radius: 6px; line-height: 1.3; max-width: 100%;">
            <i class="fas fa-folder mr-1"></i> {{ $data->category ? $data->category->name : __('No Category') }}
        </span>
    </td>
    <td style="vertical-align: middle;">
        <span class="badge badge-secondary font-weight-bold d-inline-block text-wrap" style="font-size: 12px; padding: 6px 10px; border-radius: 6px; line-height: 1.3; max-width: 100%;">
            <i class="fas fa-folder-open mr-1"></i> {{ $data->subcategory ? $data->subcategory->name : __('No Subcategory') }}
        </span>
    </td>
    <td style="vertical-align: middle;">
        <div class="font-weight-bold text-dark" style="font-size: 14px;">{{ $data->name }}</div>
        <div class="small text-muted font-italic" style="word-break: break-all;">{{ $data->slug }}</div>
    </td>

    <td class="text-center" style="vertical-align: middle;">
        <div class="dropdown">
            <button class="btn btn-{{ $data->status == 1 ? 'success' : 'danger' }} btn-sm dropdown-toggle font-weight-bold px-2 py-1" type="button" id="dropdownMenuButton_{{ $data->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: 12px;">
              {{ $data->status == 1 ? __('Enabled') : __('Disabled') }}
            </button>
            <div class="dropdown-menu animated--fade-in dropdown-menu-right" aria-labelledby="dropdownMenuButton_{{ $data->id }}">
              <a class="dropdown-item text-success font-weight-bold" href="{{ route('seller.childcategory.status', [$data->id, 1]) }}"><i class="fas fa-check mr-1"></i> {{ __('Enable') }}</a>
              <a class="dropdown-item text-danger font-weight-bold" href="{{ route('seller.childcategory.status', [$data->id, 0]) }}"><i class="fas fa-times mr-1"></i> {{ __('Disable') }}</a>
            </div>
        </div>
    </td>
    <td class="text-center" style="vertical-align: middle;">
        <div class="d-flex justify-content-center align-items-center" style="gap: 5px;">
            <a class="btn btn-primary btn-sm px-2 py-1" href="{{ route('seller.childcategory.edit', $data->id) }}" title="{{ __('Edit') }}">
                <i class="fas fa-edit"></i>
            </a>
            <a class="btn btn-danger btn-sm px-2 py-1" data-toggle="modal" data-target="#confirm-delete" href="javascript:;" data-href="{{ route('seller.childcategory.destroy', $data->id) }}" title="{{ __('Delete') }}">
                <i class="fas fa-trash-alt"></i>
            </a>
        </div>
    </td>
</tr>
@endforeach
