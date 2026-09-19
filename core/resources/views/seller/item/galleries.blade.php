@extends('master.seller')

@section('styles')
<style>
    .gallery-preview-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 8px;
        overflow: hidden;
    }
    .gallery-preview-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.12);
    }
    .gallery-img-wrap {
        height: 160px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-bottom: 1px solid #e2e8f0;
    }
    .gallery-img-wrap img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }
    .file-upload-zone {
        border: 2px dashed #0d6efd;
        border-radius: 10px;
        padding: 30px 20px;
        text-align: center;
        background: #f0f7ff;
        cursor: pointer;
        transition: background-color 0.2s, border-color 0.2s;
    }
    .file-upload-zone:hover {
        background: #e0effe;
        border-color: #0b5ed7;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h3 class="mb-1 bc-title font-weight-bold">
                        <i class="fas fa-images text-primary mr-2"></i> {{ __('Gallery Images for:') }} {{ $item->name }}
                    </h3>
                    <div class="text-muted font-size-sm">
                        {{ __('Item SKU / ID:') }} <span class="badge badge-light border font-weight-bold">#{{ $item->id }}</span> | 
                        {{ __('Type:') }} <span class="badge badge-info text-white text-capitalize">{{ $item->item_type }}</span>
                    </div>
                </div>
                <div class="mt-2 mt-sm-0">
                    <a href="{{ route('seller.item.edit', $item->id) }}" class="btn btn-outline-primary btn-sm mr-2 font-weight-bold">
                        <i class="fas fa-edit mr-1"></i> {{ __('Edit Product') }}
                    </a>
                    <a href="{{ route('seller.item.index') }}" class="btn btn-primary btn-sm font-weight-bold">
                        <i class="fas fa-chevron-left mr-1"></i> {{ __('Back to Products') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- Featured Photo & Gallery Upload -->
    <div class="row">
        <!-- Main Featured Image Preview Card -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-star mr-1 text-warning"></i> {{ __('Main Featured Image') }}</h6>
                </div>
                <div class="card-body text-center p-4">
                    <div class="p-2 border rounded bg-light mb-3" style="min-height: 180px; display: flex; align-items: center; justify-content: center;">
                        <img src="{{ $item->photo ? asset('core/public/storage/images/' . $item->photo) : asset('core/public/storage/images/placeholder.png') }}" class="img-fluid rounded" style="max-height: 180px; object-fit: contain;">
                    </div>
                    <span class="badge badge-success px-3 py-1 mb-2">{{ __('Primary Display Photo') }}</span>
                    <p class="text-muted font-size-sm mb-0">{{ __('This is the main thumbnail displayed on catalog and listing cards.') }}</p>
                </div>
            </div>
        </div>

        <!-- Upload Additional Gallery Images -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-cloud-upload-alt mr-1"></i> {{ __('Upload Additional Gallery Images') }}</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('seller.item.galleries.update') }}" method="POST" enctype="multipart/form-data" id="sellerGalleryUploadForm">
                        @csrf
                        <input type="hidden" name="item_id" value="{{ $item->id }}">

                        <label for="seller_galleries_input" class="file-upload-zone mb-3 d-block w-100 text-center" id="seller_gallery_drop_zone" style="cursor: pointer; user-select: none;">
                            <i class="fas fa-images text-primary fa-3x mb-2 d-block"></i>
                            <h5 class="font-weight-bold text-dark mb-1">{{ __('Click to Browse & Select Gallery Images') }}</h5>
                            <p class="text-muted font-size-sm mb-2">{{ __('You can select multiple photos at once (JPG, PNG, WebP, GIF, SVG).') }}</p>
                            <span class="btn btn-outline-primary btn-sm font-weight-bold px-3">
                                <i class="fas fa-folder-open mr-1"></i> {{ __('Select Images') }}
                            </span>
                            <input type="file" name="galleries[]" id="seller_galleries_input" style="position: absolute; width: 0.1px; height: 0.1px; opacity: 0; overflow: hidden; z-index: -1;" accept="image/*" multiple>
                        </label>

                        <!-- Live selection preview -->
                        <div id="seller_selection_preview_box" class="mb-3 d-none">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="font-weight-bold text-dark m-0">
                                    <i class="fas fa-check-circle text-success mr-1"></i> {{ __('Selected Photos to Upload:') }} 
                                    <span id="seller_selected_count_badge" class="badge badge-primary ml-1">0</span>
                                </h6>
                                <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 font-weight-bold" onclick="clearAllPendingSellerGalleryFiles()" style="font-size: 11.5px;">
                                    <i class="fas fa-trash-alt mr-1"></i> {{ __('Clear All') }}
                                </button>
                            </div>
                            <div class="d-flex flex-wrap" id="seller_selection_thumbs_container" style="gap: 10px;"></div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top">
                            <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> {{ __('Recommended image dimension: 800 x 800 or square ratio.') }}</small>
                            <button type="submit" id="seller_gallery_submit_btn" class="btn btn-success px-4 font-weight-bold">
                                <i class="fas fa-upload mr-1"></i> {{ __('Upload to Gallery') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Existing Gallery Images -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-dark">
                <i class="fas fa-th-large mr-1 text-primary"></i> {{ __('Current Gallery Photos') }} 
                <span class="badge badge-primary ml-1">{{ $item->galleries->count() }}</span>
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="row">
                @forelse($item->galleries as $gallery)
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-4">
                        <div class="card border gallery-preview-card h-100 shadow-sm">
                            <div class="gallery-img-wrap p-2">
                                <img src="{{ asset('core/public/storage/images/' . $gallery->photo) }}" class="img-fluid" alt="{{ __('Gallery Image') }}">
                            </div>
                            <div class="p-2 text-center bg-white">
                                <form action="{{ route('seller.item.gallery.delete', $gallery->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this gallery photo?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm btn-block font-weight-bold py-1">
                                        <i class="fas fa-trash-alt mr-1"></i> {{ __('Delete') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 text-muted">
                        <i class="fas fa-images fa-4x mb-3 text-muted" style="opacity: 0.5;"></i>
                        <h5 class="font-weight-bold text-dark">{{ __('No Gallery Images Uploaded Yet') }}</h5>
                        <p class="text-muted font-size-sm mb-0">{{ __('Use the upload box above to add additional pictures for this product.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let sellerGalleryDt = null;
try {
    sellerGalleryDt = new DataTransfer();
} catch (e) {
    sellerGalleryDt = null;
}

document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('seller_galleries_input');
    const dropZone = document.getElementById('seller_gallery_drop_zone');
    const form = document.getElementById('sellerGalleryUploadForm');
    const submitBtn = document.getElementById('seller_gallery_submit_btn');

    if (input) {
        input.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                handleSelectedSellerFiles(this.files);
            }
        });
    }

    if (dropZone) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.style.background = '#dbeafe';
                dropZone.style.borderColor = '#2563eb';
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.style.background = '#f0f7ff';
                dropZone.style.borderColor = '#0d6efd';
            }, false);
        });

        dropZone.addEventListener('drop', function (e) {
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length > 0) {
                handleSelectedSellerFiles(dt.files);
            }
        }, false);
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            const hasDtFiles = (sellerGalleryDt && sellerGalleryDt.files && sellerGalleryDt.files.length > 0);
            const hasInputFiles = (input && input.files && input.files.length > 0);

            if (!hasDtFiles && !hasInputFiles) {
                e.preventDefault();
                alert('{{ __("Please select at least one image to upload.") }}');
                return false;
            }

            if (sellerGalleryDt && sellerGalleryDt.files && sellerGalleryDt.files.length > 0 && input) {
                try {
                    input.files = sellerGalleryDt.files;
                } catch (err) {
                    console.warn('Could not assign DataTransfer to input.files', err);
                }
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> {{ __("Uploading Photos...") }}';
            }
        });
    }
});

function compressImageFile(file, maxDimension = 1400, quality = 0.85) {
    return new Promise((resolve) => {
        if (!file || !file.type.match(/^image\/(jpeg|png|webp|jpg)$/i) || file.size < 400 * 1024) {
            resolve(file);
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            const img = new Image();
            img.onload = function () {
                let width = img.width;
                let height = img.height;

                if (width > maxDimension || height > maxDimension) {
                    if (width > height) {
                        height = Math.round((height * maxDimension) / width);
                        width = maxDimension;
                    } else {
                        width = Math.round((width * maxDimension) / height);
                        height = maxDimension;
                    }
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob(function (blob) {
                    if (blob && blob.size < file.size) {
                        const newName = file.name.replace(/\.[^/.]+$/, "") + ".jpg";
                        const compressedFile = new File([blob], newName, {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        });
                        resolve(compressedFile);
                    } else {
                        resolve(file);
                    }
                }, 'image/jpeg', quality);
            };
            img.onerror = function () {
                resolve(file);
            };
            img.src = e.target.result;
        };
        reader.onerror = function () {
            resolve(file);
        };
        reader.readAsDataURL(file);
    });
}

async function handleSelectedSellerFiles(files) {
    if (!files || files.length === 0) return;

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const processedFile = await compressImageFile(file);

        if (sellerGalleryDt) {
            try {
                sellerGalleryDt.items.add(processedFile);
            } catch (err) {
                console.warn('Could not add file to DataTransfer', err);
            }
        }
    }

    const input = document.getElementById('seller_galleries_input');
    if (input && sellerGalleryDt && sellerGalleryDt.files.length > 0) {
        try {
            input.files = sellerGalleryDt.files;
        } catch (err) {
            console.warn('Could not sync input.files', err);
        }
    }

    renderSellerGalleryPreviews();
}

function renderSellerGalleryPreviews() {
    const box = document.getElementById('seller_selection_preview_box');
    const container = document.getElementById('seller_selection_thumbs_container');
    const countBadge = document.getElementById('seller_selected_count_badge');
    const input = document.getElementById('seller_galleries_input');

    if (!container) return;
    container.innerHTML = '';

    const currentFiles = (sellerGalleryDt && sellerGalleryDt.files && sellerGalleryDt.files.length > 0) 
        ? sellerGalleryDt.files 
        : (input && input.files ? input.files : []);

    if (currentFiles.length > 0) {
        if (box) box.classList.remove('d-none');
        if (countBadge) countBadge.innerText = currentFiles.length;

        Array.from(currentFiles).forEach((file, idx) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const thumb = document.createElement('div');
                thumb.className = 'border rounded p-1 bg-white shadow-sm position-relative';
                thumb.style.width = '75px';
                thumb.style.height = '75px';
                thumb.style.display = 'flex';
                thumb.style.alignItems = 'center';
                thumb.style.justifyContent = 'center';
                thumb.style.overflow = 'hidden';
                thumb.innerHTML = `
                    <img src="${e.target.result}" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="${file.name}">
                    <span onclick="removePendingSellerGalleryFile(${idx})" class="badge badge-danger position-absolute" style="top: 2px; right: 2px; cursor: pointer; padding: 2px 5px; font-size: 11px; font-weight: bold; border-radius: 50%;" title="Remove">&times;</span>
                `;
                container.appendChild(thumb);
            };
            reader.readAsDataURL(file);
        });
    } else {
        if (box) box.classList.add('d-none');
    }
}

function removePendingSellerGalleryFile(idxToRemove) {
    const input = document.getElementById('seller_galleries_input');

    if (sellerGalleryDt) {
        const currentFiles = Array.from(sellerGalleryDt.files);
        currentFiles.splice(idxToRemove, 1);

        try {
            sellerGalleryDt = new DataTransfer();
            currentFiles.forEach(f => sellerGalleryDt.items.add(f));

            if (input) {
                input.files = sellerGalleryDt.files;
            }
        } catch (err) {
            console.warn('Error rebuilding DataTransfer', err);
        }
    } else if (input) {
        input.value = '';
    }

    renderSellerGalleryPreviews();
}

function clearAllPendingSellerGalleryFiles() {
    const input = document.getElementById('seller_galleries_input');
    if (sellerGalleryDt) {
        try {
            sellerGalleryDt = new DataTransfer();
        } catch (err) {}
    }
    if (input) {
        input.value = '';
    }
    renderSellerGalleryPreviews();
}
</script>
@endsection
