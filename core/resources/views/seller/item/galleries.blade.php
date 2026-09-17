@extends('master.seller')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 text-dark"><b><i class="fas fa-images text-primary mr-2"></i> {{ __('Gallery Images for:') }} {{ $item->name }}</b></h3>
                <div>
                    <a href="{{ route('seller.item.edit', $item->id) }}" class="btn btn-outline-primary btn-sm mr-2">
                        <i class="fas fa-edit mr-1"></i> {{ __('Edit Product') }}
                    </a>
                    <a href="{{ route('seller.item.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-chevron-left mr-1"></i> {{ __('Back to Products') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- Upload New Gallery Images -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white py-3">
            <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-upload mr-1"></i> {{ __('Upload Additional Gallery Images') }}</h6>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('seller.item.galleries.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="item_id" value="{{ $item->id }}">

                <div class="form-group mb-3">
                    <label class="font-weight-bold">{{ __('Select Images (Multiple allowed)') }} <span class="text-danger">*</span></label>
                    <div class="custom-file">
                        <input type="file" name="galleries[]" id="galleries" class="custom-file-input" accept="image/*" multiple required>
                        <label class="custom-file-label" for="galleries">{{ __('Choose Images...') }}</label>
                    </div>
                    <small class="text-muted">{{ __('Hold Ctrl/Cmd to select multiple pictures. Recommended resolution: 800x800.') }}</small>
                </div>

                <button type="submit" class="btn btn-success px-4 font-weight-bold">
                    <i class="fas fa-upload mr-1"></i> {{ __('Upload to Gallery') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Existing Gallery Images -->
    <div class="card shadow-sm">
        <div class="card-header bg-light py-3">
            <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-th mr-1"></i> {{ __('Current Gallery Images') }} ({{ $item->galleries->count() }})</h6>
        </div>
        <div class="card-body p-4">
            <div class="row">
                @forelse($item->galleries as $gallery)
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-4">
                        <div class="card border h-100 p-2 text-center shadow-sm">
                            <img src="{{ asset('core/public/storage/images/' . $gallery->photo) }}" class="img-fluid rounded mb-2" style="height: 120px; object-fit: cover; width: 100%;">
                            <a href="{{ route('seller.item.gallery.delete', $gallery->id) }}" class="btn btn-danger btn-xs btn-block" onclick="return confirm('{{ __('Are you sure you want to delete this gallery photo?') }}')">
                                <i class="fas fa-trash"></i> {{ __('Delete') }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-muted">
                        <i class="fas fa-images fa-3x mb-2 d-block"></i>
                        {{ __('No gallery images uploaded yet for this product.') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
