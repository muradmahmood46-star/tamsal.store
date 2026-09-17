@extends('master.seller')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 text-dark"><b><i class="fas fa-edit text-primary mr-2"></i> {{ __('Edit Product:') }} {{ $item->name }}</b></h3>
                <div>
                    <a href="{{ route('seller.item.gallery', $item->id) }}" class="btn btn-warning btn-sm mr-2">
                        <i class="fas fa-images mr-1"></i> {{ __('Manage Gallery') }}
                    </a>
                    <a href="{{ route('seller.item.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-chevron-left mr-1"></i> {{ __('Back to Products') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    @if($item->approval_status == 'Rejected' && $item->reject_reason)
        <div class="alert alert-danger shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x mr-3 text-danger"></i>
                <div>
                    <h6 class="mb-1 font-weight-bold text-danger">{{ __('Product Requires Changes (Rejected by Admin)') }}</h6>
                    <p class="mb-1 text-dark"><strong>{{ __('Admin Feedback / Reason:') }}</strong> {{ $item->reject_reason }}</p>
                    <small class="text-muted">{{ __('Please review the issue described above, make the appropriate edits, and click "Update Product" to resubmit it for admin verification.') }}</small>
                </div>
            </div>
        </div>
    @elseif($item->approval_status == 'Pending')
        <div class="alert alert-warning shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-clock fa-2x mr-3 text-warning"></i>
                <div>
                    <h6 class="mb-1 font-weight-bold text-dark">{{ __('Product is Currently Under Admin Review') }}</h6>
                    <p class="mb-0 text-muted small">{{ __('This product is currently pending admin approval. Saving updates will refresh its verification request.') }}</p>
                </div>
            </div>
        </div>
    @endif

    <form class="tab-form" action="{{ route('seller.item.update', $item->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="item_type" value="{{ $item->item_type }}">
        <input type="hidden" name="is_button" id="is_button" value="0">

        <div class="row">
            <!-- Left Main Column -->
            <div class="col-lg-8">
                <!-- Title & Slug -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="name" class="font-weight-bold">{{ __('Product Name / Title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control item-name" value="{{ old('name', $item->name) }}" required>
                        </div>
                        <div class="form-group mb-0">
                            <label for="slug" class="font-weight-bold">{{ __('Slug') }} <span class="text-danger">*</span></label>
                            <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $item->slug) }}" required>
                        </div>
                    </div>
                </div>

                <!-- Featured Photo -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-2">
                        <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-image text-primary mr-1"></i> {{ __('Product Image') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold">{{ __('Current Featured Image') }}</label>
                            <div class="mb-3 text-center">
                                <img src="{{ $item->photo ? asset('core/public/storage/images/' . $item->photo) : asset('core/public/storage/images/placeholder.png') }}" id="featured_preview" class="img-fluid rounded border p-1" style="max-height: 180px;">
                            </div>
                            <div class="custom-file">
                                <input type="file" name="photo" id="photo" class="custom-file-input upload-photo" accept="image/*" onchange="previewImg(this, 'featured_preview')">
                                <label class="custom-file-label" for="photo">{{ __('Change Main Image (800x800 recommended)...') }}</label>
                            </div>
                            <small class="text-info mt-1 d-block">{{ __('Leave empty to keep existing image.') }}</small>
                        </div>
                    </div>
                </div>

                <!-- Descriptions -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-2">
                        <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-align-left text-primary mr-1"></i> {{ __('Product Descriptions') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="sort_details" class="font-weight-bold">{{ __('Short Description') }} <span class="text-danger">*</span></label>
                            <textarea name="sort_details" id="sort_details" rows="3" class="form-control" required>{{ old('sort_details', $item->sort_details) }}</textarea>
                        </div>

                        <div class="form-group mb-0">
                            <label for="details" class="font-weight-bold">{{ __('Full Description') }} <span class="text-danger">*</span></label>
                            <textarea name="details" id="details" rows="6" class="form-control" placeholder="{{ __('Full detailed description of product features, material, specs, etc.') }}">{{ old('details', $item->details) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Product Tags & Specifications -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-2">
                        <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-tags text-primary mr-1"></i> {{ __('Product Tags & Specifications') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="tags" class="font-weight-bold">{{ __('Product Tags') }}</label>
                            <input type="text" name="tags" class="tags" id="tags" placeholder="{{ __('Tags (press comma or enter to separate)') }}" value="{{ $item->tags }}">
                            <small class="text-muted">{{ __('e.g. summer, cotton, stylish, new-arrival') }}</small>
                        </div>

                        <div class="form-group mb-2">
                            <label class="switch-primary">
                                <input type="checkbox" class="switch switch-bootstrap status radio-check" name="is_specification" value="1" {{ $item->is_specification == 1 ? 'checked' : '' }}>
                                <span class="switch-body"></span>
                                <span class="switch-text font-weight-bold text-dark">{{ __('Enable Specifications Table') }}</span>
                            </label>
                        </div>

                        <div id="specifications-section" class="{{ $item->is_specification == 0 ? 'd-none' : '' }}">
                            @if(!empty($specification_name) && is_array($specification_name))
                                @foreach(array_combine($specification_name, $specification_description) as $name => $description)
                                    <div class="d-flex mb-2">
                                        <div class="flex-grow-1 mr-2">
                                            <input type="text" class="form-control form-control-sm" name="specification_name[]" placeholder="{{ __('Specification Name') }}" value="{{ $name }}">
                                        </div>
                                        <div class="flex-grow-1 mr-2">
                                            <input type="text" class="form-control form-control-sm" name="specification_description[]" placeholder="{{ __('Specification Description') }}" value="{{ $description }}">
                                        </div>
                                        <div class="flex-btn">
                                            @if($loop->first)
                                                <button type="button" class="btn btn-success btn-sm add-specification" data-text="{{ __('Specification Name') }}" data-text1="{{ __('Specification Description') }}">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-danger btn-sm remove-spcification" data-text="{{ __('Specification Name') }}" data-text1="{{ __('Specification Description') }}">
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 mr-2">
                                        <input type="text" class="form-control form-control-sm" name="specification_name[]" placeholder="{{ __('Specification Name') }}" value="">
                                    </div>
                                    <div class="flex-grow-1 mr-2">
                                        <input type="text" class="form-control form-control-sm" name="specification_description[]" placeholder="{{ __('Specification Description') }}" value="">
                                    </div>
                                    <div class="flex-btn">
                                        <button type="button" class="btn btn-success btn-sm add-specification" data-text="{{ __('Specification Name') }}" data-text1="{{ __('Specification Description') }}">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @php
                    $existingVariants = [];
                    if (!empty($item->item_variants)) {
                        $existingVariants = json_decode($item->item_variants, true) ?? [];
                    }
                    $hasVariants = !empty($existingVariants);
                @endphp

                <!-- Product Variants (Size & Color with Stock) -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                        <h6 class="mb-0 font-weight-bold text-dark"><i class="fas fa-layer-group text-primary mr-2"></i>{{ __('Product Variants (Size & Color with Stock)') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="switch-primary">
                                <input type="checkbox" class="switch switch-bootstrap status radio-check" id="is_variant_toggle" name="is_variant" value="1" {{ $hasVariants ? 'checked' : '' }} onchange="toggleVariantSection(this)">
                                <span class="switch-body"></span>
                                <span class="switch-text font-weight-bold text-dark">{{ __('Enable Size & Color Variants for this product') }}</span>
                            </label>
                            <p class="text-muted mb-0 small">{{ __('Enable this if this product has multiple colors or sizes with individual stock quantities (e.g. Black - M - 5 pcs, Black - L - 3 pcs).') }}</p>
                        </div>

                        <div id="variants_section_wrapper" style="display: {{ $hasVariants ? 'block' : 'none' }};">
                            {{-- Quick Combination Generator --}}
                            <div class="p-3 mb-3 border rounded shadow-sm" style="background-color: #f8fafc; border: 1px dashed #0284c7 !important;">
                                <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-magic text-warning mr-1"></i> {{ __('Quick Combinations Generator') }}</h6>
                                <div class="row align-items-end">
                                    <div class="col-lg-5 col-md-5 col-sm-12 mb-2">
                                        <label class="font-weight-bold small text-dark mb-1">{{ __('Colors (comma separated)') }}</label>
                                        <input type="text" id="gen_colors" class="form-control form-control-sm" placeholder="{{ __('e.g. Black, White, Blue') }}">
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12 mb-2">
                                        <label class="font-weight-bold small text-dark mb-1">{{ __('Sizes (comma separated)') }}</label>
                                        <input type="text" id="gen_sizes" class="form-control form-control-sm" placeholder="{{ __('e.g. S, M, L, XL') }}">
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-12 mb-2">
                                        <button type="button" class="btn btn-primary btn-sm w-100 font-weight-bold text-nowrap d-flex align-items-center justify-content-center" style="height: 31px;" onclick="generateVariantRows()">
                                            <i class="fas fa-bolt mr-1"></i> {{ __('Generate') }}
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-1">{{ __('Tip: Enter Colors and/or Sizes above and click Generate to auto-create variant combinations.') }}</small>
                            </div>

                            {{-- Variants Table --}}
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm text-center" id="variants_table">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="min-width: 130px;">{{ __('Color') }}</th>
                                            <th style="min-width: 130px;">{{ __('Size') }}</th>
                                            <th style="min-width: 120px;">{{ __('Stock (pcs)') }} *</th>
                                            <th style="min-width: 120px;">{{ __('Extra Price') }} ({{ PriceHelper::adminCurrency() }})</th>
                                            <th style="width: 50px;">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="variants_tbody">
                                        {{-- Dynamically populated rows --}}
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
                                <button type="button" class="btn btn-outline-success btn-sm font-weight-bold mb-1" onclick="addSingleVariantRow('', '', 0, 0)">
                                    <i class="fas fa-plus"></i> {{ __('+ Add Custom Variant Row') }}
                                </button>
                                <div class="text-right mb-1">
                                    <span class="font-weight-bold" style="font-size: 13.5px;">{{ __('Total Variant Stock:') }} <span id="total_variant_stock_display" class="text-primary font-weight-bold">0</span> {{ __('pcs') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rating & Review Management (Demo / Initial Rating) -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                        <h6 class="mb-0 font-weight-bold text-dark"><i class="fas fa-star text-warning mr-2"></i>{{ __('Rating & Reviews Management') }}</h6>
                        <span class="badge badge-warning text-dark font-weight-bold">{{ __('Custom Rating') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="switch-primary">
                                <input type="checkbox" class="switch switch-bootstrap status radio-check" id="is_custom_rating_toggle" name="is_custom_rating" value="1" {{ $item->is_custom_rating == 1 ? 'checked' : '' }} onchange="toggleRatingSection(this)">
                                <span class="switch-body"></span>
                                <span class="switch-text font-weight-bold text-dark" style="font-size: 14.5px;">{{ __('Enable Custom / Demo Rating for this product') }}</span>
                            </label>
                            <p class="text-muted mb-0" style="font-size: 13px;">{{ __('Enable this to set custom star rating and review count for this product. Great for newly launched products and marketing initial trust.') }}</p>
                        </div>

                        <div id="custom_rating_wrapper" style="display: {{ $item->is_custom_rating == 1 ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold text-dark mb-1">{{ __('Star Rating (1.0 to 5.0)') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-warning text-dark"><i class="fas fa-star"></i></span>
                                        </div>
                                        <input type="number" step="0.1" min="1" max="5" id="custom_rating_input" name="custom_rating" class="form-control" value="{{ $item->custom_rating ?? 5.0 }}" placeholder="e.g. 4.8">
                                    </div>
                                    <div class="mt-2">
                                        <span class="small font-weight-bold text-muted mr-1">{{ __('Quick Presets:') }}</span>
                                        <button type="button" class="btn btn-outline-warning btn-xs py-0 px-2 text-dark font-weight-bold" onclick="setRatingPreset(5.0)">5.0 ★</button>
                                        <button type="button" class="btn btn-outline-warning btn-xs py-0 px-2 text-dark font-weight-bold" onclick="setRatingPreset(4.9)">4.9 ★</button>
                                        <button type="button" class="btn btn-outline-warning btn-xs py-0 px-2 text-dark font-weight-bold" onclick="setRatingPreset(4.8)">4.8 ★</button>
                                        <button type="button" class="btn btn-outline-warning btn-xs py-0 px-2 text-dark font-weight-bold" onclick="setRatingPreset(4.5)">4.5 ★</button>
                                        <button type="button" class="btn btn-outline-warning btn-xs py-0 px-2 text-dark font-weight-bold" onclick="setRatingPreset(4.0)">4.0 ★</button>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold text-dark mb-1">{{ __('Total Review Count') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-info text-white"><i class="fas fa-comments"></i></span>
                                        </div>
                                        <input type="number" min="0" id="custom_rating_count_input" name="custom_rating_count" class="form-control" value="{{ $item->custom_rating_count ?? 25 }}" placeholder="e.g. 48">
                                    </div>
                                    <div class="mt-2">
                                        <span class="small font-weight-bold text-muted mr-1">{{ __('Quick Presets:') }}</span>
                                        <button type="button" class="btn btn-outline-info btn-xs py-0 px-2 font-weight-bold" onclick="setReviewCountPreset(12)">12</button>
                                        <button type="button" class="btn btn-outline-info btn-xs py-0 px-2 font-weight-bold" onclick="setReviewCountPreset(25)">25</button>
                                        <button type="button" class="btn btn-outline-info btn-xs py-0 px-2 font-weight-bold" onclick="setReviewCountPreset(48)">48</button>
                                        <button type="button" class="btn btn-outline-info btn-xs py-0 px-2 font-weight-bold" onclick="setReviewCountPreset(96)">96</button>
                                        <button type="button" class="btn btn-outline-info btn-xs py-0 px-2 font-weight-bold" onclick="setReviewCountPreset(150)">150+</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Current Reviews Table --}}
                            @if ($item->reviews->count() > 0)
                                <div class="mb-3 border rounded p-2" style="background: #ffffff;">
                                    <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-list-alt text-primary mr-1"></i> {{ __('Current Reviews on this Product') }} ({{ $item->reviews->count() }})</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm text-center mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>{{ __('Reviewer') }}</th>
                                                    <th>{{ __('Rating') }}</th>
                                                    <th>{{ __('Subject') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($item->reviews as $rev)
                                                    <tr>
                                                        <td>
                                                            <span class="font-weight-bold">{{ $rev->reviewer_name }}</span>
                                                            @if($rev->is_admin_added == 1)
                                                                <span class="badge badge-info ml-1" style="font-size: 10px;">{{ __('Demo') }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-warning font-weight-bold">{{ $rev->rating }} ★</td>
                                                        <td><small>{{ Str::limit($rev->subject, 30) }}</small></td>
                                                        <td>
                                                            @if($rev->status == 1)
                                                                <span class="badge badge-success">{{ __('Live') }}</span>
                                                            @else
                                                                <span class="badge badge-warning">{{ __('Pending') }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            {{-- Custom Demo Reviews Creator --}}
                            <div class="p-3 mt-2 border rounded" style="background-color: #fcfcfd; border: 1px dashed #d97706 !important;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="font-weight-bold text-dark mb-0"><i class="fas fa-user-edit text-warning mr-1"></i> {{ __('Add Demo Customer Reviews (Optional)') }}</h6>
                                    <button type="button" class="btn btn-success btn-xs font-weight-bold" onclick="addDemoReviewRow()">
                                        <i class="fas fa-plus"></i> {{ __('+ Add Review') }}
                                    </button>
                                </div>
                                <p class="text-muted small mb-2">{{ __('You can add realistic customer testimonials that will appear on the product page.') }}</p>
                                
                                <div id="demo_reviews_container">
                                    {{-- Dynamically appended demo review cards --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Return Policy Management Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                        <h6 class="mb-0 font-weight-bold text-dark"><i class="fas fa-undo-alt text-primary mr-2"></i>{{ __('Return Policy') }}</h6>
                        <span class="badge badge-primary font-weight-bold">{{ __('Easy Return') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="switch-primary">
                                <input type="checkbox" class="switch switch-bootstrap status radio-check" id="is_returnable_toggle" name="is_returnable" value="1" {{ $item->is_returnable == 1 ? 'checked' : '' }} onchange="toggleReturnPolicySection(this)">
                                <span class="switch-body"></span>
                                <span class="switch-text font-weight-bold text-dark" style="font-size: 14.5px;">{{ __('Enable Return Policy for this product') }}</span>
                            </label>
                            <p class="text-muted mb-0" style="font-size: 13px;">{{ __('Enable this if you offer easy return / replacement guarantee for this product.') }}</p>
                        </div>

                        <div id="return_policy_wrapper" style="display: {{ $item->is_returnable == 1 ? 'block' : 'none' }};">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold text-dark mb-1">{{ __('Return Window (Number of Days)') }} *</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary text-white"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="number" min="1" max="365" id="return_days_input" name="return_days" class="form-control" value="{{ $item->return_days ?? 14 }}" placeholder="e.g. 14">
                                    <div class="input-group-append">
                                        <span class="input-group-text font-weight-bold">{{ __('Days Easy Return') }}</span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <span class="small font-weight-bold text-muted mr-1">{{ __('Quick Presets:') }}</span>
                                    <button type="button" class="btn btn-outline-primary btn-xs py-0 px-2 font-weight-bold" onclick="setReturnDaysPreset(1)">1 Day</button>
                                    <button type="button" class="btn btn-outline-primary btn-xs py-0 px-2 font-weight-bold" onclick="setReturnDaysPreset(3)">3 Days</button>
                                    <button type="button" class="btn btn-outline-primary btn-xs py-0 px-2 font-weight-bold" onclick="setReturnDaysPreset(5)">5 Days</button>
                                    <button type="button" class="btn btn-outline-primary btn-xs py-0 px-2 font-weight-bold" onclick="setReturnDaysPreset(7)">7 Days</button>
                                    <button type="button" class="btn btn-outline-primary btn-xs py-0 px-2 font-weight-bold" onclick="setReturnDaysPreset(14)">14 Days</button>
                                    <button type="button" class="btn btn-outline-primary btn-xs py-0 px-2 font-weight-bold" onclick="setReturnDaysPreset(30)">30 Days</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO Meta Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-2">
                        <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-search text-primary mr-1"></i> {{ __('SEO Meta Information') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="meta_keywords" class="font-weight-bold">{{ __('Meta Keywords') }}</label>
                            <input type="text" name="meta_keywords" class="tags" id="meta_keywords" placeholder="{{ __('Enter Meta Keywords') }}" value="{{ $item->meta_keywords }}">
                            <small class="text-muted">{{ __('Keywords to help search engines index this product.') }}</small>
                        </div>

                        <div class="form-group mb-0">
                            <label for="meta_description" class="font-weight-bold">{{ __('Meta Description') }}</label>
                            <textarea name="meta_description" id="meta_description" class="form-control" rows="4" placeholder="{{ __('Enter Meta Description for SEO') }}">{{ $item->meta_description }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar Column -->
            <div class="col-lg-4">
                <!-- Action Buttons -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block py-2 font-weight-bold mb-2" onclick="$('#is_button').val(0)">
                            <i class="fas fa-save mr-1"></i> {{ __('Update & View All Products') }}
                        </button>
                        <button type="submit" class="btn btn-outline-primary btn-block py-2 font-weight-bold" onclick="$('#is_button').val(1)">
                            <i class="fas fa-edit mr-1"></i> {{ __('Update & Continue Editing') }}
                        </button>
                    </div>
                </div>

                <!-- Pricing, Advance Payment & Delivery Fees -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-2">
                        <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-tag mr-1"></i> {{ __('Pricing & Delivery') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="discount_price" class="font-weight-bold">{{ __('Current Price') }} ({{ PriceHelper::adminCurrency() }}) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="1" name="discount_price" id="discount_price" class="form-control font-weight-bold" value="{{ old('discount_price', $item->discount_price) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="previous_price" class="font-weight-bold">{{ __('Previous / Original Price') }} ({{ PriceHelper::adminCurrency() }})</label>
                            <input type="number" step="0.01" min="0" name="previous_price" id="previous_price" class="form-control" value="{{ old('previous_price', $item->previous_price) }}">
                            <small class="text-muted">{{ __('Optional strikethrough price (e.g. was 1500, now 1200).') }}</small>
                        </div>


                        <!-- Delivery Fees Setting -->
                        <div class="form-group mb-0 border-top pt-3">
                            <label class="font-weight-bold">{{ __('Delivery Fees') }}</label>
                            <div class="mb-2">
                                <div class="custom-control custom-radio mb-2">
                                    <input type="radio" id="delivery_type_amount" name="is_free_delivery" class="custom-control-input" value="0" {{ ($item->is_free_delivery ?? 0) == 0 ? 'checked' : '' }} onchange="document.getElementById('delivery_fee_input_group').style.display = 'flex';">
                                    <label class="custom-control-label font-weight-bold" style="white-space: normal; line-height: 1.4;" for="delivery_type_amount">{{ __('Delivery Charges (PKR)') }}</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="delivery_type_free" name="is_free_delivery" class="custom-control-input" value="1" {{ ($item->is_free_delivery ?? 0) == 1 ? 'checked' : '' }} onchange="document.getElementById('delivery_fee_input_group').style.display = 'none';">
                                    <label class="custom-control-label text-success font-weight-bold" style="white-space: normal; line-height: 1.4;" for="delivery_type_free">{{ __('Free Delivery') }}</label>
                                </div>
                            </div>
                            <div class="input-group mb-0" id="delivery_fee_input_group" style="display: {{ ($item->is_free_delivery ?? 0) == 1 ? 'none' : 'flex' }};">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{ PriceHelper::adminCurrency() }}</span>
                                </div>
                                <input type="number" id="delivery_fee" name="delivery_fee" class="form-control" placeholder="{{ __('Enter delivery fee e.g. 150, 200, 300') }}" min="0" step="1" value="{{ old('delivery_fee', $item->delivery_fee ?? 0) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category & Brand -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-2">
                        <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-list mr-1"></i> {{ __('Category & Brand') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="category_id" class="font-weight-bold mb-0">{{ __('Category') }} <span class="text-danger">*</span></label>
                                <a href="{{ route('seller.category.create') }}" target="_blank" class="text-primary small font-weight-bold"><i class="fas fa-plus-circle"></i> {{ __('+ Add Category') }}</a>
                            </div>
                            <select name="category_id" id="category_id" class="form-control" required onchange="loadSubcategories(this.value)">
                                <option value="">{{ __('-- Select Category --') }}</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $item->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="subcategory_id" class="font-weight-bold mb-0">{{ __('Subcategory') }}</label>
                                <a href="{{ route('seller.subcategory.create') }}" target="_blank" class="text-primary small font-weight-bold"><i class="fas fa-plus-circle"></i> {{ __('+ Add Subcategory') }}</a>
                            </div>
                            <select name="subcategory_id" id="subcategory_id" class="form-control" onchange="loadChildCategories(this.value)">
                                <option value="">{{ __('-- Select Subcategory --') }}</option>
                                @if($item->category && $item->category->subcategory)
                                    @foreach($item->category->subcategory as $subcat)
                                        <option value="{{ $subcat->id }}" {{ $item->subcategory_id == $subcat->id ? 'selected' : '' }}>{{ $subcat->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="childcategory_id" class="font-weight-bold mb-0">{{ __('Childcategory') }}</label>
                                <a href="{{ route('seller.childcategory.create') }}" target="_blank" class="text-primary small font-weight-bold"><i class="fas fa-plus-circle"></i> {{ __('+ Add Childcategory') }}</a>
                            </div>
                            <select name="childcategory_id" id="childcategory_id" class="form-control">
                                <option value="">{{ __('-- Select Childcategory --') }}</option>
                                @if($item->subcategory && $item->subcategory->childcategory)
                                    @foreach($item->subcategory->childcategory as $childcat)
                                        <option value="{{ $childcat->id }}" {{ $item->childcategory_id == $childcat->id ? 'selected' : '' }}>{{ $childcat->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="form-group mb-0">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="brand_id" class="font-weight-bold mb-0">{{ __('Brand') }}</label>
                                <a href="{{ route('seller.brand.create') }}" target="_blank" class="text-primary small font-weight-bold"><i class="fas fa-plus-circle"></i> {{ __('+ Add Brand') }}</a>
                            </div>
                            <select name="brand_id" id="brand_id" class="form-control">
                                <option value="">{{ __('-- Select Brand (Optional) --') }}</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ $item->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Inventory, Tax, SKU & Video -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-2">
                        <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-boxes mr-1"></i> {{ __('Inventory & Details') }}</h6>
                    </div>
                    <div class="card-body">
                        @if($item->item_type != 'digital')
                            <div class="form-group mb-3">
                                <label for="stock" class="font-weight-bold">{{ __('Total in stock') }} <span class="text-danger">*</span></label>
                                <input type="number" min="0" step="1" name="stock" id="stock" class="form-control font-weight-bold" value="{{ old('stock', $item->stock) }}" required>
                            </div>
                        @else
                            <input type="hidden" name="stock" id="stock" value="999999">
                        @endif

                        <input type="hidden" name="tax_id" value="{{ $item->tax_id ?? ($taxes->where('status', 1)->first()->id ?? ($taxes->first()->id ?? '')) }}">

                        <div class="form-group mb-3">
                            <label for="sku" class="font-weight-bold">{{ __('SKU') }} <span class="text-danger">*</span></label>
                            <input type="text" name="sku" id="sku" class="form-control" value="{{ old('sku', $item->sku) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="video" class="font-weight-bold">{{ __('Video Link') }}</label>
                            <input type="text" name="video" id="video" class="form-control" placeholder="{{ __('e.g. YouTube video embed link') }}" value="{{ old('video', $item->video) }}">
                        </div>

                        <div class="form-group mb-0">
                            <label for="estimated_profit" class="font-weight-bold text-success">
                                <i class="fas fa-coins mr-1"></i> {{ __('Estimated Profit') }} <span class="badge badge-danger text-white ml-1" style="font-size: 11px;">{{ __('Only for Admin & Vendor Use') }}</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light font-weight-bold text-success">{{ $curr->sign ?? 'PKR' }}</span>
                                </div>
                                <input type="number" step="0.01" min="0" name="estimated_profit" class="form-control"
                                    id="estimated_profit" placeholder="{{ __('Enter Estimated Profit per Unit (e.g. 150.00)') }}"
                                    value="{{ old('estimated_profit', $item->estimated_profit ?? 0) }}">
                            </div>
                            <small class="form-text text-muted font-italic">
                                <i class="fas fa-shield-alt text-primary mr-1"></i> {{ __('Only for internal admin/vendor calculations (never shown to buyers/users on product page). When an order with this product is accepted, this profit is added to your store total earnings.') }}
                            </small>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function previewImg(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const el = document.getElementById(previewId);
                el.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Auto slug generator
    $('#name').on('keyup', function() {
        var val = $(this).val();
        var slug = val.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
        $('#slug').val(slug);
    });

    function loadSubcategories(catId) {
        if (!catId) {
            $('#subcategory_id').html('<option value="">-- Select Subcategory --</option>');
            $('#childcategory_id').html('<option value="">-- Select Childcategory --</option>');
            return;
        }

        $.ajax({
            url: "{{ route('seller.get.subcategory') }}",
            type: "GET",
            data: { category_id: catId },
            success: function(response) {
                var html = '<option value="">-- Select Subcategory --</option>';
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(item) {
                        html += '<option value="' + item.id + '">' + item.name + '</option>';
                    });
                }
                $('#subcategory_id').html(html);
                $('#childcategory_id').html('<option value="">-- Select Childcategory --</option>');
            }
        });
    }

    function loadChildCategories(subId) {
        if (!subId) {
            $('#childcategory_id').html('<option value="">-- Select Childcategory --</option>');
            return;
        }

        $.ajax({
            url: "{{ route('seller.get.childcategory') }}",
            type: "GET",
            data: { subcategory_id: subId },
            success: function(response) {
                var html = '<option value="">-- Select Childcategory --</option>';
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(item) {
                        html += '<option value="' + item.id + '">' + item.name + '</option>';
                    });
                }
                $('#childcategory_id').html(html);
            }
        });
    }

    // -----------------------------------------------------------------
    // Product Variants (Size, Color & Stock)
    // -----------------------------------------------------------------
    function toggleVariantSection(checkbox) {
        var wrapper = document.getElementById('variants_section_wrapper');
        if (checkbox.checked) {
            wrapper.style.display = 'block';
            if (document.querySelectorAll('#variants_tbody tr').length === 0) {
                addSingleVariantRow('', '', 0, 0);
            }
            calculateTotalVariantStock();
        } else {
            wrapper.style.display = 'none';
        }
    }

    function addSingleVariantRow(color, size, stock, price) {
        var tbody = document.getElementById('variants_tbody');
        var row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <input type="text" name="variant_color[]" class="form-control form-control-sm variant-color-input" placeholder="e.g. Black" value="${escapeHtml(color)}">
            </td>
            <td>
                <input type="text" name="variant_size[]" class="form-control form-control-sm variant-size-input" placeholder="e.g. M" value="${escapeHtml(size)}">
            </td>
            <td>
                <input type="number" name="variant_stock[]" class="form-control form-control-sm variant-stock-input text-center font-weight-bold" min="0" step="1" placeholder="0" value="${stock}" oninput="calculateTotalVariantStock()" required>
            </td>
            <td>
                <input type="number" name="variant_price[]" class="form-control form-control-sm text-center" min="0" step="0.1" placeholder="0" value="${price}">
            </td>
            <td>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeVariantRow(this)" title="Delete"><i class="fas fa-trash"></i></button>
            </td>
        `;
        tbody.appendChild(row);
        calculateTotalVariantStock();
    }

    function removeVariantRow(btn) {
        var row = btn.closest('tr');
        row.remove();
        calculateTotalVariantStock();
    }

    function generateVariantRows() {
        var colorsInput = document.getElementById('gen_colors').value.trim();
        var sizesInput = document.getElementById('gen_sizes').value.trim();

        var colors = colorsInput ? colorsInput.split(',').map(function(s) { return s.trim(); }).filter(function(s) { return s.length > 0; }) : [];
        var sizes = sizesInput ? sizesInput.split(',').map(function(s) { return s.trim(); }).filter(function(s) { return s.length > 0; }) : [];

        if (colors.length === 0 && sizes.length === 0) {
            alert('Please enter at least one Color or Size.');
            return;
        }

        var tbody = document.getElementById('variants_tbody');
        tbody.innerHTML = '';

        if (colors.length > 0 && sizes.length > 0) {
            colors.forEach(function(color) {
                sizes.forEach(function(size) {
                    addSingleVariantRow(color, size, 0, 0);
                });
            });
        } else if (colors.length > 0) {
            colors.forEach(function(color) {
                addSingleVariantRow(color, '', 0, 0);
            });
        } else if (sizes.length > 0) {
            sizes.forEach(function(size) {
                addSingleVariantRow('', size, 0, 0);
            });
        }

        calculateTotalVariantStock();
    }

    function calculateTotalVariantStock() {
        var isVariantChecked = document.getElementById('is_variant_toggle').checked;
        if (!isVariantChecked) return;

        var stockInputs = document.querySelectorAll('.variant-stock-input');
        var total = 0;
        stockInputs.forEach(function(inp) {
            var val = parseInt(inp.value);
            if (!isNaN(val) && val > 0) {
                total += val;
            }
        });

        var displayEl = document.getElementById('total_variant_stock_display');
        if (displayEl) {
            displayEl.innerText = total;
        }

        var mainStockInput = document.getElementById('stock');
        if (mainStockInput && stockInputs.length > 0) {
            mainStockInput.value = total;
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    // -----------------------------------------------------------------
    // Rating & Reviews Management Functions
    // -----------------------------------------------------------------
    function toggleRatingSection(el) {
        if (el.checked) {
            $('#custom_rating_wrapper').slideDown();
        } else {
            $('#custom_rating_wrapper').slideUp();
        }
    }

    function setRatingPreset(val) {
        $('#custom_rating_input').val(val);
    }

    function setReviewCountPreset(val) {
        $('#custom_rating_count_input').val(val);
    }

    // Return Policy Functions
    function toggleReturnPolicySection(el) {
        if (el.checked) {
            $('#return_policy_wrapper').slideDown();
        } else {
            $('#return_policy_wrapper').slideUp();
        }
    }

    function setReturnDaysPreset(days) {
        $('#return_days_input').val(days);
    }

    let demoReviewIndex = 0;
    function addDemoReviewRow(name = '', rating = 5, subject = '', review = '') {
        demoReviewIndex++;
        const html = `
            <div class="card border mb-2 demo-review-card shadow-sm" id="demo_review_card_${demoReviewIndex}" style="background: #ffffff;">
                <div class="card-body p-2">
                    <div class="row align-items-center mb-2">
                        <div class="col-md-5 mb-1">
                            <label class="small font-weight-bold text-dark mb-0">{{ __("Customer / Reviewer Name") }} *</label>
                            <input type="text" name="demo_reviewer_name[]" class="form-control form-control-sm" placeholder="e.g. Ahmed Khan" value="${escapeHtml(name)}" required>
                        </div>
                        <div class="col-md-3 mb-1">
                            <label class="small font-weight-bold text-dark mb-0">{{ __("Rating Stars") }}</label>
                            <select name="demo_rating[]" class="form-control form-control-sm">
                                <option value="5" ${rating == 5 ? 'selected' : ''}>⭐⭐⭐⭐⭐ (5 Stars)</option>
                                <option value="4" ${rating == 4 ? 'selected' : ''}>⭐⭐⭐⭐ (4 Stars)</option>
                                <option value="3" ${rating == 3 ? 'selected' : ''}>⭐⭐⭐ (3 Stars)</option>
                                <option value="2" ${rating == 2 ? 'selected' : ''}>⭐⭐ (2 Stars)</option>
                                <option value="1" ${rating == 1 ? 'selected' : ''}>⭐ (1 Star)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-1">
                            <label class="small font-weight-bold text-dark mb-0">{{ __("Title / Headline") }}</label>
                            <input type="text" name="demo_subject[]" class="form-control form-control-sm" placeholder="e.g. Excellent Quality & Fast Delivery!" value="${escapeHtml(subject)}">
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-11 mb-1">
                            <label class="small font-weight-bold text-dark mb-0">{{ __("Review Comment") }} *</label>
                            <textarea name="demo_review[]" class="form-control form-control-sm" rows="2" placeholder="e.g. Ordered this and absolutely loved the fabric and quality! Will definitely buy again." required>${escapeHtml(review)}</textarea>
                        </div>
                        <div class="col-md-1 text-center mb-1">
                            <button type="button" class="btn btn-outline-danger btn-sm p-1 mt-3" title="{{ __('Remove') }}" onclick="$('#demo_review_card_${demoReviewIndex}').remove()">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#demo_reviews_container').append(html);
    }

    document.addEventListener("DOMContentLoaded", function() {
        var initialVariants = {!! json_encode($existingVariants) !!};
        if (initialVariants && initialVariants.length > 0) {
            initialVariants.forEach(function(v) {
                addSingleVariantRow(v.color || '', v.size || '', v.stock !== undefined ? v.stock : 0, v.price !== undefined ? v.price : 0);
            });
        }
    });
</script>
@endsection

