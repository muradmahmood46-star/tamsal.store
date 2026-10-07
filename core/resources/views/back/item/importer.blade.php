@extends('master.back')

@section('styles')
<style>
    .importer-hero {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border-radius: 14px;
        color: #fff;
        padding: 24px;
        margin-bottom: 25px;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.2);
    }
    .importer-nav-tabs {
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
    }
    .importer-nav-tabs .nav-link {
        color: rgba(255, 255, 255, 0.85);
        font-weight: 600;
        font-size: 15px;
        border: none;
        padding: 10px 20px;
        border-radius: 8px 8px 0 0;
        transition: all 0.2s ease-in-out;
    }
    .importer-nav-tabs .nav-link:hover {
        color: #fff;
        background: rgba(255, 255, 255, 0.1);
    }
    .importer-nav-tabs .nav-link.active {
        color: #1e3c72;
        background: #fff;
        font-weight: 700;
    }
    .tab-content-box {
        background: #fff;
        border-radius: 0 10px 10px 10px;
        padding: 22px;
        color: #333;
    }
    .image-picker-card {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        position: relative;
        background: #fff;
    }
    .image-picker-card:hover {
        border-color: #3b82f6;
        transform: translateY(-2px);
    }
    .image-picker-card.selected-main {
        border-color: #10b981;
        background: #ecfdf5;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
    }
    .image-picker-card .main-badge {
        display: none;
        position: absolute;
        top: 6px;
        left: 6px;
        background: #10b981;
        color: #fff;
        font-size: 11px;
        font-weight: bold;
        padding: 2px 7px;
        border-radius: 4px;
        z-index: 2;
    }
    .image-picker-card.selected-main .main-badge {
        display: block;
    }
    .image-picker-card .gallery-badge {
        position: absolute;
        top: 6px;
        left: 6px;
        background: #3b82f6;
        color: #fff;
        font-size: 11px;
        font-weight: bold;
        padding: 2px 7px;
        border-radius: 4px;
        z-index: 2;
    }
    .image-picker-card.selected-main .gallery-badge {
        display: none;
    }
    .image-picker-img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 6px;
    }
    .gallery-checkbox-wrap {
        position: absolute;
        top: 6px;
        right: 6px;
        z-index: 2;
    }
    .guide-step-card {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 13px;
    }
    .gallery-row-card {
        border-left: 4px solid #3b82f6;
        border-radius: 6px;
        background: #f8fafc;
        transition: all 0.2s ease-in-out;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 bc-title"><b><i class="fas fa-magic text-warning mr-2"></i>{{ __('1-Click Smart Product Importer') }}</b></h3>
                <div>
                    <a class="btn btn-outline-primary btn-sm mr-2" href="{{ route('back.item.create') }}"><i class="fas fa-plus"></i> {{ __('Manual Create Product') }}</a>
                    <a class="btn btn-secondary btn-sm" href="{{ route('back.item.index') }}"><i class="fas fa-chevron-left"></i> {{ __('Back to Products') }}</a>
                </div>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- Importer Hero & Dual Tabs -->
    <div class="importer-hero">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="font-weight-bold mb-0"><i class="fas fa-bolt text-warning mr-1"></i> {{ __('Smart Product Auto-Importer') }}</h4>
            </div>
            <span class="badge badge-light text-dark font-weight-bold py-2 px-3 d-none d-md-inline-block" style="font-size: 13px;">
                <i class="fas fa-shield-alt text-success mr-1"></i> {{ __('100% Safe Preview Before Publish') }}
            </span>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav importer-nav-tabs" id="importerTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="hhc-tab" data-toggle="tab" href="#tab-hhc-text" role="tab" aria-controls="tab-hhc-text" aria-selected="true">
                    <i class="fas fa-clipboard-check text-warning mr-1"></i> {{ __('Smart Text / HHC Copy-Paste (Recommended for HHC)') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="url-tab" data-toggle="tab" href="#tab-web-url" role="tab" aria-controls="tab-web-url" aria-selected="false">
                    <i class="fas fa-link text-info mr-1"></i> {{ __('Import via Web URL (Daraz, Shopify, AliExpress)') }}
                </a>
            </li>
        </ul>

        <!-- Tab Contents -->
        <div class="tab-content shadow-sm" id="importerTabContent">
            
            <!-- TAB 1: Smart Text / Structured Input -->
            <div class="tab-pane fade show active tab-content-box" id="tab-hhc-text" role="tabpanel" aria-labelledby="hhc-tab">
                <div class="row">
                    <div class="col-12">
                        
                        <!-- 1. Product Name / Title -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark mb-1">
                                <i class="fas fa-heading text-primary mr-1"></i> {{ __('Product Title / Name') }} *
                            </label>
                            <input type="text" id="input_product_title" class="form-control form-control-lg font-weight-bold">
                        </div>

                        <!-- 2. Short Description -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark mb-1">
                                <i class="fas fa-align-left text-info mr-1"></i> {{ __('Short Description') }}
                            </label>
                            <textarea id="input_product_sort_details" class="form-control" rows="2"></textarea>
                        </div>

                        <!-- 3. Full Main Description -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark mb-1">
                                <i class="fas fa-file-alt text-success mr-1"></i> {{ __('Main Description / Full Details') }} *
                            </label>
                            <textarea id="input_product_details" class="form-control" rows="4"></textarea>
                        </div>

                        <!-- 4. Supplier / Source Product Page Link -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark mb-1">
                                <i class="fas fa-link text-info mr-1"></i> {{ __('Supplier / Source Product Page Link') }} <small class="text-muted">({{ __('Optional - e.g. HHC / Daraz product page URL') }})</small>
                            </label>
                            <input type="url" id="input_supplier_url" class="form-control" placeholder="{{ __('https://hhcdropshipping.com/product/...') }}">
                        </div>

                        <!-- Optional: Bulk Paste helper -->
                        <div class="mb-3">
                            <a class="text-primary font-weight-bold small" data-toggle="collapse" href="#bulk_paste_collapse" role="button" aria-expanded="false" aria-controls="bulk_paste_collapse">
                                <i class="fas fa-paste mr-1"></i> {{ __('+ Or Paste Raw Bulk Text from HHC / Supplier (Auto-Extracts All Fields)') }}
                            </a>
                            <div class="collapse mt-2" id="bulk_paste_collapse">
                                <div class="card card-body p-2 bg-light border">
                                    <textarea id="raw_text_input" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Main Image URL Input -->
                        <div class="card mb-2 border-success shadow-sm" style="border-width: 2px;">
                            <div class="card-header bg-success text-white py-2 d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold"><i class="fas fa-star text-warning mr-1"></i> {{ __('Main Featured Photo URL (Primary Image)') }} *</span>
                                <button type="button" class="btn btn-warning btn-sm font-weight-bold py-1 px-3 shadow-sm" id="btn_add_first_gallery" onclick="addGalleryImageInput()">
                                    <i class="fas fa-plus-circle mr-1"></i> <span id="add_gallery_btn_label">{{ __('+ Add Gallery Image') }}</span>
                                </button>
                            </div>
                            <div class="card-body p-3">
                                <div class="row align-items-center">
                                    <div class="col-md-9">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light"><i class="fas fa-link text-primary"></i></span>
                                            </div>
                                            <input type="url" id="input_main_image_url" class="form-control" placeholder="{{ __('Right-click Main Photo on HHC -> Copy Image Address -> Paste here') }}" oninput="previewMainPhoto(this.value)">
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-center mt-2 mt-md-0">
                                        <div id="main_photo_preview_box" class="border rounded p-1 bg-light d-flex align-items-center justify-content-center" style="height: 70px; background: #fff;">
                                            <span class="text-muted small" id="main_photo_placeholder"><i class="fas fa-image fa-2x text-black-50 d-block"></i> {{ __('Main Preview') }}</span>
                                            <img id="main_photo_preview_img" src="" class="img-fluid rounded" style="max-height: 65px; display: none;" onerror="hideMainPreview()" onload="showMainPreview()">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Gallery Images Container -->
                        <div id="gallery_inputs_container" class="mb-3">
                            <!-- Dynamic Gallery 1, Gallery 2, Gallery 3 rows will be inserted here -->
                        </div>

                        <!-- Extra Add More Button (visible when rows exist) -->
                        <div id="add_more_gallery_btn_wrap" class="mb-3" style="display: none;">
                            <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold" onclick="addGalleryImageInput()">
                                <i class="fas fa-plus mr-1"></i> <span id="add_more_btn_label">{{ __('+ Add Gallery Image') }}</span>
                            </button>
                        </div>

                        <!-- Parse & Auto-Fill Action Button -->
                        <div class="mt-4">
                            <button type="button" class="btn btn-primary btn-lg font-weight-bold px-4 shadow-sm" id="parse_text_btn" onclick="parseProductText()">
                                <span id="parse_btn_spinner" class="spinner-border spinner-border-sm mr-1 d-none" role="status"></span>
                                <span id="parse_btn_text"><i class="fas fa-magic mr-1"></i> {{ __('⚡ Parse & Auto-Fill Product Details (Auto SEO Tags & Meta)') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Direct Web URL -->
            <div class="tab-pane fade tab-content-box" id="tab-web-url" role="tabpanel" aria-labelledby="url-tab">
                <label class="font-weight-bold text-dark mb-1">
                    <i class="fas fa-globe text-primary mr-1"></i> {{ __('Product Page URL:') }}
                </label>
                <div class="input-group input-group-lg">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-light border-right-0 text-primary"><i class="fas fa-link"></i></span>
                    </div>
                    <input type="url" id="scrape_url_input" class="form-control" placeholder="{{ __('Paste link from Daraz, AliExpress, Shopify stores, etc.') }}">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-warning font-weight-bold px-4" id="fetch_btn" onclick="fetchProductData()">
                            <span id="fetch_btn_spinner" class="spinner-border spinner-border-sm mr-1 d-none" role="status"></span>
                            <span id="fetch_btn_text"><i class="fas fa-cloud-download-alt mr-1"></i> {{ __('Fetch from Link') }}</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Alert Box for AJAX errors -->
    <div id="fetch_error_alert" class="alert alert-danger d-none shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle mr-1"></i> <span id="fetch_error_msg"></span>
    </div>

    <!-- Product Preview & Publishing Form (Initially Hidden until Fetched/Parsed) -->
    <div id="product_preview_container" style="display: none;">
        <form action="{{ route('back.product.importer.store') }}" method="POST" enctype="multipart/form-data" id="importer_publish_form">
            @csrf
            <input type="hidden" name="main_image_url" id="selected_main_image_url" value="">
            <input type="hidden" name="is_button" id="is_button_val" value="0">

            <div class="row">
                <!-- Left Column -->
                <div class="col-lg-8">
                    <!-- Title & Source Card -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-heading text-primary mr-2"></i>{{ __('Product Title & Identity') }}</h5>
                            <span class="badge badge-success font-weight-bold px-2 py-1" id="auto_matched_badge">{{ __('Auto-Extracted') }}</span>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="imp_name" class="font-weight-bold text-dark">{{ __('Product Title / Name') }} *</label>
                                <input type="text" name="name" id="imp_name" class="form-control form-control-lg font-weight-bold" placeholder="{{ __('Enter Product Name') }}" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="imp_sku" class="font-weight-bold text-dark">{{ __('SKU / Product Code') }} *</label>
                                        <input type="text" name="sku" id="imp_sku" class="form-control text-uppercase font-weight-bold" value="{{ \App\Repositories\Back\ItemRepository::generateAutoSku() }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="imp_product_from" class="font-weight-bold text-info"><i class="fas fa-truck-loading mr-1"></i>{{ __('Product From (Supplier)') }}</label>
                                        <input type="text" name="product_from" id="imp_product_from" class="form-control" value="HHC Dropshipping" placeholder="e.g. HHC Dropshipping">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mt-3 mb-0">
                                <label for="imp_supplier_url" class="font-weight-bold text-info d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-link mr-1"></i>{{ __('Supplier / Source Product URL') }}</span>
                                    <span class="badge badge-secondary" style="font-size: 11px;">{{ __('Admin Only - Visible in Order Invoices for 1-Click Ordering') }}</span>
                                </label>
                                <input type="url" name="supplier_url" id="imp_supplier_url" class="form-control" placeholder="{{ __('https://hhcdropshipping.com/product/...') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Images Selection & Upload Card -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-images text-primary mr-2"></i>{{ __('Product Photos & Gallery') }}</h5>
                            <small class="text-muted">{{ __('Click an image to set as Main Featured photo') }}</small>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-2">
                                <span class="badge badge-success mr-1"><i class="fas fa-star"></i> {{ __('Green border') }} = {{ __('Main Featured Photo') }}</span>
                                <span class="badge badge-primary mr-1"><i class="fas fa-images"></i> {{ __('Blue badge') }} = {{ __('Gallery Photos') }}</span>
                            </p>
                            
                            <!-- Extracted Images Grid -->
                            <div class="row" id="images_grid_container">
                                {{-- Dynamically populated image cards --}}
                            </div>

                            <!-- Manual Upload Fallback -->
                            <div class="border-top pt-3 mt-3">
                                <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-upload text-muted mr-1"></i> {{ __('Or Upload Images Directly from Computer:') }}</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-2 mb-md-0">
                                        <label class="small font-weight-bold text-muted">{{ __('Upload Main Featured Photo:') }}</label>
                                        <input type="file" name="photo" class="form-control-file" accept="image/*">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-muted">{{ __('Upload Extra Gallery Photos:') }}</label>
                                        <input type="file" name="galleries[]" multiple class="form-control-file" accept="image/*">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description Card -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-align-left text-primary mr-2"></i>{{ __('Descriptions & Details') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="imp_sort_details" class="font-weight-bold text-dark">{{ __('Short Description') }} *</label>
                                <textarea name="sort_details" id="imp_sort_details" class="form-control" rows="3" placeholder="{{ __('Short summary for quick view...') }}" required></textarea>
                            </div>

                            <div class="form-group mb-0">
                                <label for="imp_details" class="font-weight-bold text-dark">{{ __('Full Description & Specifications') }} *</label>
                                <textarea name="details" id="imp_details" class="form-control" rows="8" placeholder="{{ __('Enter full product description...') }}" required></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Product Tags & Specifications Card -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-tags text-primary mr-2"></i>{{ __('Product Tags & Specifications') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="imp_tags" class="font-weight-bold text-dark">{{ __('Product Tags') }}</label>
                                <input type="text" name="tags" class="tags" id="imp_tags" placeholder="{{ __('Tags') }}">
                                <small class="text-muted">{{ __('Max 5 truly relatable tags. Click × on any tag to remove, or type and press Enter/comma to add more.') }}</small>
                            </div>

                            <div class="form-group mb-2">
                                <label class="switch-primary">
                                    <input type="checkbox" class="switch switch-bootstrap status radio-check" id="is_specification_toggle" name="is_specification" value="1" onchange="toggleSpecificationSection(this)">
                                    <span class="switch-body"></span>
                                    <span class="switch-text font-weight-bold text-dark">{{ __('Specifications') }}</span>
                                </label>
                                <p class="text-muted mb-0 small">{{ __('Enable this to add key-value technical specifications (e.g. Material: Cotton, Weight: 250g).') }}</p>
                            </div>

                            <div id="specifications-section" style="display: none;">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1 mr-2">
                                        <div class="form-group mb-0">
                                            <input type="text" class="form-control" name="specification_name[]" placeholder="{{ __('Specification Name') }}">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 mr-2">
                                        <div class="form-group mb-0">
                                            <input type="text" class="form-control" name="specification_description[]" placeholder="{{ __('Specification description') }}">
                                        </div>
                                    </div>
                                    <div class="flex-btn">
                                        <button type="button" class="btn btn-success add-specification" data-text="{{ __('Specification Name') }}" data-text1="{{ __('Specification Description') }}">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Variants (Size, Color & Stock) Card -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-layer-group text-primary mr-2"></i>{{ __('Product Variants (Size & Color with Stock)') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label class="switch-primary">
                                    <input type="checkbox" class="switch switch-bootstrap status radio-check" id="is_variant_toggle" name="is_variant" value="1" onchange="toggleVariantSection(this)">
                                    <span class="switch-body"></span>
                                    <span class="switch-text font-weight-bold text-dark">{{ __('Enable Size & Color Variants for this product') }}</span>
                                </label>
                                <p class="text-muted mb-0 small">{{ __('Enable this if this product has multiple colors or sizes with individual stock quantities (e.g. Black - M - 5 pcs, Black - L - 3 pcs).') }}</p>
                            </div>

                            <div id="variants_section_wrapper" style="display: none;">
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
                                                <th style="min-width: 120px;">{{ __('Extra Price') }} ({{ $curr->sign ?? 'PKR' }})</th>
                                                <th style="width: 50px;">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="variants_tbody">
                                            {{-- Dynamically populated rows --}}
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <button type="button" class="btn btn-outline-success btn-sm font-weight-bold" onclick="addSingleVariantRow('', '', 0, 0)">
                                        <i class="fas fa-plus"></i> {{ __('+ Add Custom Variant Row') }}
                                    </button>
                                    <div class="text-right">
                                        <span class="font-weight-bold" style="font-size: 13.5px;">{{ __('Total Variant Stock:') }} <span id="total_variant_stock_display" class="text-primary font-weight-bold">0</span> {{ __('pcs') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rating & Review Management (Demo / Initial Rating) -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-star text-warning mr-2"></i>{{ __('Rating & Reviews Management') }}</h5>
                            <span class="badge badge-warning text-dark font-weight-bold">{{ __('Admin Managed') }}</span>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label class="switch-primary">
                                    <input type="checkbox" class="switch switch-bootstrap status radio-check" id="is_custom_rating_toggle" name="is_custom_rating" value="1" checked onchange="toggleRatingSection(this)">
                                    <span class="switch-body"></span>
                                    <span class="switch-text font-weight-bold text-dark" style="font-size: 14.5px;">{{ __('Enable Custom / Demo Rating for this product') }}</span>
                                </label>
                                <p class="text-muted mb-0" style="font-size: 13px;">{{ __('Enable this to set custom star rating and review count for this product. Great for newly launched products and marketing initial trust.') }}</p>
                            </div>

                            <div id="custom_rating_wrapper" style="display: block;">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold text-dark mb-1">{{ __('Star Rating (1.0 to 5.0)') }}</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-warning text-dark"><i class="fas fa-star"></i></span>
                                            </div>
                                            <input type="number" step="0.1" min="1" max="5" id="custom_rating_input" name="custom_rating" class="form-control" value="4.9" placeholder="e.g. 4.9">
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
                                            <input type="number" min="0" id="custom_rating_count_input" name="custom_rating_count" class="form-control" value="32" placeholder="e.g. 32">
                                        </div>
                                        <div class="mt-2">
                                            <span class="small font-weight-bold text-muted mr-1">{{ __('Quick Presets:') }}</span>
                                            <button type="button" class="btn btn-outline-info btn-xs py-0 px-2 font-weight-bold" onclick="setReviewCountPreset(12)">12</button>
                                            <button type="button" class="btn btn-outline-info btn-xs py-0 px-2 font-weight-bold" onclick="setReviewCountPreset(25)">25</button>
                                            <button type="button" class="btn btn-outline-info btn-xs py-0 px-2 font-weight-bold" onclick="setReviewCountPreset(32)">32</button>
                                            <button type="button" class="btn btn-outline-info btn-xs py-0 px-2 font-weight-bold" onclick="setReviewCountPreset(48)">48</button>
                                            <button type="button" class="btn btn-outline-info btn-xs py-0 px-2 font-weight-bold" onclick="setReviewCountPreset(96)">96</button>
                                        </div>
                                    </div>
                                </div>

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
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-undo-alt text-primary mr-2"></i>{{ __('Return Policy') }}</h5>
                            <span class="badge badge-primary font-weight-bold">{{ __('Easy Return') }}</span>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label class="switch-primary">
                                    <input type="checkbox" class="switch switch-bootstrap status radio-check" id="is_returnable_toggle" name="is_returnable" value="1" checked onchange="toggleReturnPolicySection(this)">
                                    <span class="switch-body"></span>
                                    <span class="switch-text font-weight-bold text-dark" style="font-size: 14.5px;">{{ __('Enable Return Policy for this product') }}</span>
                                </label>
                                <p class="text-muted mb-0" style="font-size: 13px;">{{ __('Enable this if you offer easy return / replacement guarantee for this product.') }}</p>
                            </div>

                            <div id="return_policy_wrapper" style="display: block;">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold text-dark mb-1">{{ __('Return Window (Number of Days)') }} *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary text-white"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="number" min="1" max="365" id="return_days_input" name="return_days" class="form-control" value="14" placeholder="e.g. 14">
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

                    <!-- SEO Meta Keywords & Search Ranking Card -->
                    <div class="card mb-4 shadow-sm border-info">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center py-2">
                            <h5 class="mb-0 font-weight-bold"><i class="fas fa-search text-warning mr-2"></i>{{ __('SEO Meta Keywords & Search Ranking') }}</h5>
                            <span class="badge badge-light text-dark font-weight-bold"><i class="fas fa-robot text-primary mr-1"></i> {{ __('Auto-Generated') }}</span>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="imp_meta_keywords" class="font-weight-bold text-dark">{{ __('Meta Keywords') }}</label>
                                <input type="text" name="meta_keywords" class="tags" id="imp_meta_keywords" placeholder="{{ __('Enter Meta Keywords') }}">
                                <small class="text-muted">{{ __('Max 5 high-intent search queries. Click × on any tag to remove, or type and press Enter/comma to add more.') }}</small>
                            </div>

                            <div class="form-group mb-0">
                                <label for="imp_meta_description" class="font-weight-bold text-dark">{{ __('Meta Description') }}</label>
                                <textarea name="meta_description" id="imp_meta_description" class="form-control" rows="3" placeholder="{{ __('Enter Meta Description') }}"></textarea>
                                <small class="text-muted">{{ __('100% perfectly fit Google search snippet under 160 characters.') }}</small>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column (Settings & Pricing) -->
                <div class="col-lg-4">
                    <!-- Action Buttons Card -->
                    <div class="card mb-4 shadow-sm border-primary">
                        <div class="card-body text-center p-3">
                            <button type="submit" class="btn btn-success btn-block btn-lg font-weight-bold mb-2 shadow-sm" onclick="document.getElementById('is_button_val').value = '0';">
                                <i class="fas fa-rocket mr-1"></i> {{ __('Publish Product to Website') }}
                            </button>
                            <button type="submit" class="btn btn-outline-info btn-block font-weight-bold" onclick="document.getElementById('is_button_val').value = '1';">
                                <i class="fas fa-edit mr-1"></i> {{ __('Save & Open Full Editor') }}
                            </button>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-check text-success mr-1"></i> {{ __('Orders will directly appear in Admin Panel') }}
                            </small>
                        </div>
                    </div>

                    <!-- Pricing & Profit Calculator -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-tags text-success mr-2"></i>{{ __('Pricing & Profit Margin') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-2">
                                <label class="font-weight-bold text-muted small">{{ __('Supplier Cost / Wholesale Price') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light font-weight-bold">{{ $curr->sign ?? 'PKR' }}</span>
                                    </div>
                                    <input type="number" id="imp_cost_price" class="form-control bg-light font-weight-bold" placeholder="0" oninput="calculateProfit()">
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="imp_discount_price" class="font-weight-bold text-primary">{{ __('Your Selling Price (On Website)') }} *</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary text-white font-weight-bold">{{ $curr->sign ?? 'PKR' }}</span>
                                    </div>
                                    <input type="number" step="1" name="discount_price" id="imp_discount_price" class="form-control form-control-lg font-weight-bold text-primary" placeholder="e.g. 999" required oninput="calculateProfit()">
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="imp_previous_price" class="font-weight-bold text-muted small">{{ __('Previous / Strike Price (Optional)') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ $curr->sign ?? 'PKR' }}</span>
                                    </div>
                                    <input type="number" step="1" name="previous_price" id="imp_previous_price" class="form-control" placeholder="e.g. 1499">
                                </div>
                            </div>

                            <!-- Net Profit Display -->
                            <div class="p-3 bg-light rounded border border-success text-center mb-3">
                                <span class="small font-weight-bold text-muted d-block">{{ __('Estimated Profit per Unit') }}</span>
                                <h4 class="font-weight-bold text-success mb-0" id="estimated_profit_display">Rs 0</h4>
                                <input type="hidden" name="estimated_profit" id="imp_estimated_profit" value="0">
                            </div>

                            <!-- Advance Payment Offer UI -->
                            <div class="form-group border-top pt-3 mb-0">
                                <label class="font-weight-bold text-dark mb-1">{{ __('Advance Payment Offer Less') }}</label>
                                <div class="mb-2">
                                    <div class="custom-control custom-radio mb-2">
                                        <input type="radio" id="adv_type_percentage" name="advance_payment_type" class="custom-control-input" value="percentage" checked onchange="document.getElementById('adv_payment_suffix').innerText = '%'">
                                        <label class="custom-control-label" style="white-space: normal; line-height: 1.4;" for="adv_type_percentage">{{ __('Percentage (%)') }}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="adv_type_fixed" name="advance_payment_type" class="custom-control-input" value="fixed" onchange="document.getElementById('adv_payment_suffix').innerText = '{{ $curr->sign ?? 'PKR' }}'">
                                        <label class="custom-control-label" style="white-space: normal; line-height: 1.4;" for="adv_type_fixed">{{ __('Fixed Price (PKR)') }}</label>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text font-weight-bold" id="adv_payment_suffix">%</span>
                                    </div>
                                    <input type="number" id="advance_payment_amount" name="advance_payment_amount" class="form-control" placeholder="{{ __('Enter amount e.g. 100, 200, 300') }}" min="0" step="0.1" value="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category & Subcategory Card -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-sitemap text-primary mr-2"></i>{{ __('Categories Selection') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="imp_category_id" class="font-weight-bold text-dark">{{ __('Main Category') }} * <small class="text-success font-weight-bold">({{ __('Auto-Selected') }})</small></label>
                                <select name="category_id" id="imp_category_id" data-href="{{ route('back.get.subcategory') }}" class="form-control font-weight-bold" required onchange="loadSubcategories(this.value)">
                                    <option value="">{{ __('Select One') }}</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="imp_subcategory_id" class="font-weight-bold text-dark">{{ __('Sub Category') }} <small class="text-muted">({{ __('Optional - Select manually') }})</small></label>
                                <select name="subcategory_id" id="imp_subcategory_id" data-href="{{ route('back.get.childcategory') }}" class="form-control" onchange="loadChildCategories(this.value)">
                                    <option value="">{{ __('Select One') }}</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="imp_childcategory_id" class="font-weight-bold text-dark">{{ __('Child Category') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                                <select name="childcategory_id" id="imp_childcategory_id" class="form-control">
                                    <option value="">{{ __('Select One') }}</option>
                                </select>
                            </div>

                            <div class="form-group mb-0">
                                <label for="imp_brand_id" class="font-weight-bold text-dark">{{ __('Select Brand') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                                <select name="brand_id" id="imp_brand_id" class="form-control">
                                    <option value="">{{ __('Select Brand') }}</option>
                                    @foreach(DB::table('brands')->whereStatus(1)->where(function($q){ $q->whereNull('vendor_id')->orWhere('vendor_id', 0); })->get() as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Cash on Delivery & Order Settings -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-cog text-primary mr-2"></i>{{ __('Order & Delivery Settings') }}</h5>
                        </div>
                        <div class="card-body">
                            <!-- COD Toggle (Checked by default) -->
                            <div class="form-group mb-3">
                                <label class="switch-primary d-flex align-items-center">
                                    <input type="checkbox" class="switch switch-bootstrap status radio-check" name="is_cod" value="1" checked>
                                    <span class="switch-body"></span>
                                    <span class="switch-text font-weight-bold text-dark ml-2">{{ __('Offer Cash on Delivery (COD)') }}</span>
                                </label>
                                <small class="text-muted">{{ __('Enabled by default for Pakistan orders.') }}</small>
                            </div>

                            <!-- Delivery Fee Setting -->
                            <div class="form-group mb-3 border-top pt-3">
                                <label class="font-weight-bold text-dark mb-1">{{ __('Delivery Fees') }}</label>
                                <div class="mb-2">
                                    <div class="custom-control custom-radio mb-2">
                                        <input type="radio" id="delivery_type_amount" name="is_free_delivery" class="custom-control-input" value="0" checked onchange="document.getElementById('delivery_fee_input_group').style.display = 'flex';">
                                        <label class="custom-control-label" style="white-space: normal; line-height: 1.4;" for="delivery_type_amount">{{ __('Delivery Charges (PKR)') }}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="delivery_type_free" name="is_free_delivery" class="custom-control-input" value="1" onchange="document.getElementById('delivery_fee_input_group').style.display = 'none';">
                                        <label class="custom-control-label text-success font-weight-bold" style="white-space: normal; line-height: 1.4;" for="delivery_type_free">{{ __('Free Delivery') }}</label>
                                    </div>
                                </div>
                                <div class="input-group" id="delivery_fee_input_group" style="display: flex;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text font-weight-bold">{{ $curr->sign ?? 'PKR' }}</span>
                                    </div>
                                    <input type="number" id="delivery_fee" name="delivery_fee" class="form-control" placeholder="{{ __('Enter delivery fee e.g. 150, 200, 300') }}" min="0" step="1" value="0">
                                </div>
                            </div>

                            <!-- Stock -->
                            <div class="form-group mb-0 border-top pt-3">
                                <label class="font-weight-bold text-dark">{{ __('Total Stock Quantity') }}</label>
                                <input type="number" name="stock" id="imp_stock" class="form-control" value="20" min="1">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>

</div>

<script>
    var galleryRowCount = 0;

    // Add Dynamic Gallery Image Input Row
    function addGalleryImageInput(initialUrl = '') {
        galleryRowCount++;
        var rowId = 'gallery_row_' + galleryRowCount;
        var container = document.getElementById('gallery_inputs_container');

        var card = document.createElement('div');
        card.className = 'card mb-2 gallery-row-card shadow-sm';
        card.id = rowId;
        card.innerHTML = `
            <div class="card-body p-2 px-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="font-weight-bold text-dark small gallery-label">
                        <i class="fas fa-images text-primary mr-1"></i> <span class="gallery-title-text">{{ __('Gallery Image') }} ${galleryRowCount} URL</span>
                    </span>
                    <button type="button" class="btn btn-outline-danger btn-xs py-0 px-2 font-weight-bold" onclick="removeGalleryImageInput('${rowId}')" title="{{ __('Delete') }}">
                        <i class="fas fa-times"></i> {{ __('Remove') }}
                    </button>
                </div>
                <div class="row align-items-center">
                    <div class="col-md-9">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white"><i class="fas fa-link text-info"></i></span>
                            </div>
                            <input type="url" class="form-control form-control-sm gallery-url-field" placeholder="{{ __('Right-click Gallery Photo -> Copy Image Address -> Paste here') }}" value="${escapeHtml(initialUrl)}" oninput="previewGalleryItem(this, '${rowId}')">
                        </div>
                    </div>
                    <div class="col-md-3 text-center mt-2 mt-md-0">
                        <div class="border rounded p-1 bg-white d-flex align-items-center justify-content-center gallery-thumb-box" style="height: 50px;">
                            <span class="text-muted small gallery-thumb-empty"><i class="fas fa-image text-black-50"></i> Preview</span>
                            <img src="${escapeHtml(initialUrl)}" class="img-fluid rounded gallery-thumb-img" style="max-height: 45px; display: ${initialUrl ? 'block' : 'none'};" onerror="this.style.display='none'; this.previousElementSibling.style.display='block';" onload="this.style.display='block'; this.previousElementSibling.style.display='none';">
                        </div>
                    </div>
                </div>
            </div>
        `;

        container.appendChild(card);
        updateGalleryLabels();
        document.getElementById('add_more_gallery_btn_wrap').style.display = 'block';

        if (initialUrl) {
            var imgEl = card.querySelector('.gallery-thumb-img');
            imgEl.src = initialUrl;
        }
    }

    // Remove Dynamic Gallery Row
    function removeGalleryImageInput(rowId) {
        var el = document.getElementById(rowId);
        if (el) {
            el.remove();
            updateGalleryLabels();
        }
    }

    // Update Numbering: Gallery Image 1, Gallery Image 2, etc.
    function updateGalleryLabels() {
        var rows = document.querySelectorAll('#gallery_inputs_container .gallery-row-card');
        rows.forEach(function(row, idx) {
            var num = idx + 1;
            var labelSpan = row.querySelector('.gallery-title-text');
            if (labelSpan) {
                labelSpan.innerText = '{{ __("Gallery Image") }} ' + num + ' URL';
            }
        });

        var nextNum = rows.length + 1;
        var btnLabel = document.getElementById('add_gallery_btn_label');
        if (btnLabel) {
            btnLabel.innerText = '{{ __("+ Add Gallery Image") }} ' + nextNum;
        }

        var addMoreLabel = document.getElementById('add_more_btn_label');
        if (addMoreLabel) {
            addMoreLabel.innerText = '{{ __("+ Add Gallery Image") }} ' + nextNum;
        }

        if (rows.length === 0) {
            document.getElementById('add_more_gallery_btn_wrap').style.display = 'none';
        }
    }

    // Realtime Main Photo Preview
    function previewMainPhoto(url) {
        var img = document.getElementById('main_photo_preview_img');
        var placeholder = document.getElementById('main_photo_placeholder');
        if (url && url.trim().length > 5) {
            img.src = url.trim();
        } else {
            hideMainPreview();
        }
    }

    function showMainPreview() {
        document.getElementById('main_photo_preview_img').style.display = 'block';
        document.getElementById('main_photo_placeholder').style.display = 'none';
    }

    function hideMainPreview() {
        document.getElementById('main_photo_preview_img').style.display = 'none';
        document.getElementById('main_photo_placeholder').style.display = 'block';
    }

    // Realtime Gallery Photo Preview
    function previewGalleryItem(inputEl, rowId) {
        var row = document.getElementById(rowId);
        if (!row) return;
        var img = row.querySelector('.gallery-thumb-img');
        var emptySpan = row.querySelector('.gallery-thumb-empty');
        var url = inputEl.value.trim();

        if (url.length > 5) {
            img.src = url;
        } else {
            img.style.display = 'none';
            emptySpan.style.display = 'block';
        }
    }

    // Tab 1: Parse Structured / Smart Text & SEO
    function parseProductText() {
        var inputTitle = document.getElementById('input_product_title').value.trim();
        var inputSortDetails = document.getElementById('input_product_sort_details').value.trim();
        var inputDetails = document.getElementById('input_product_details').value.trim();
        var inputSupplierUrl = document.getElementById('input_supplier_url') ? document.getElementById('input_supplier_url').value.trim() : '';
        var rawText = document.getElementById('raw_text_input') ? document.getElementById('raw_text_input').value.trim() : '';
        var mainImageUrl = document.getElementById('input_main_image_url').value.trim();
        
        // Collect dynamic gallery URLs
        var galleryUrls = [];
        document.querySelectorAll('#gallery_inputs_container .gallery-url-field').forEach(function(inp) {
            var val = inp.value.trim();
            if (val) {
                galleryUrls.push(val);
            }
        });

        var parseBtn = document.getElementById('parse_text_btn');
        var spinner = document.getElementById('parse_btn_spinner');
        var btnText = document.getElementById('parse_btn_text');
        var errorAlert = document.getElementById('fetch_error_alert');
        var errorMsg = document.getElementById('fetch_error_msg');
        var previewContainer = document.getElementById('product_preview_container');

        if (!inputTitle && !rawText && !inputDetails) {
            alert('{{ __("Please enter at least Product Title or paste product details!") }}');
            return;
        }

        parseBtn.disabled = true;
        spinner.classList.remove('d-none');
        btnText.innerText = '{{ __("Extracting SEO keywords & tags...") }}';
        errorAlert.classList.add('d-none');

        $.ajax({
            url: "{{ route('back.product.importer.parse_text') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                input_title: inputTitle,
                input_sort_details: inputSortDetails,
                input_details: inputDetails,
                input_supplier_url: inputSupplierUrl,
                raw_text: rawText,
                main_image_url: mainImageUrl,
                gallery_urls: galleryUrls
            },
            success: function(response) {
                parseBtn.disabled = false;
                spinner.classList.add('d-none');
                btnText.innerHTML = '<i class="fas fa-magic mr-1"></i> {{ __("⚡ Parse & Auto-Fill Product Details (Auto SEO Tags & Meta)") }}';

                if (response.success && response.data) {
                    populatePreviewForm(response.data);
                    previewContainer.style.display = 'block';
                    $('html, body').animate({
                        scrollTop: $("#product_preview_container").offset().top - 40
                    }, 500);
                } else {
                    errorMsg.innerText = response.message || '{{ __("Unable to process details.") }}';
                    errorAlert.classList.remove('d-none');
                }
            },
            error: function(xhr) {
                parseBtn.disabled = false;
                spinner.classList.add('d-none');
                btnText.innerHTML = '<i class="fas fa-magic mr-1"></i> {{ __("⚡ Parse & Auto-Fill Product Details (Auto SEO Tags & Meta)") }}';

                var message = '{{ __("Failed to process details.") }}';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                errorMsg.innerText = message;
                errorAlert.classList.remove('d-none');
            }
        });
    }

    // Tab 2: Fetch via URL
    function fetchProductData() {
        var urlInput = document.getElementById('scrape_url_input').value.trim();
        var fetchBtn = document.getElementById('fetch_btn');
        var spinner = document.getElementById('fetch_btn_spinner');
        var btnText = document.getElementById('fetch_btn_text');
        var errorAlert = document.getElementById('fetch_error_alert');
        var errorMsg = document.getElementById('fetch_error_msg');
        var previewContainer = document.getElementById('product_preview_container');

        if (!urlInput) {
            alert('{{ __("Please paste a product URL first!") }}');
            return;
        }

        // Show loading state
        fetchBtn.disabled = true;
        spinner.classList.remove('d-none');
        btnText.innerText = '{{ __("Fetching & Extracting...") }}';
        errorAlert.classList.add('d-none');

        $.ajax({
            url: "{{ route('back.product.importer.fetch') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                url: urlInput
            },
            success: function(response) {
                fetchBtn.disabled = false;
                spinner.classList.add('d-none');
                btnText.innerHTML = '<i class="fas fa-cloud-download-alt mr-1"></i> {{ __("Fetch from Link") }}';

                if (response.success && response.data) {
                    populatePreviewForm(response.data);
                    previewContainer.style.display = 'block';
                    $('html, body').animate({
                        scrollTop: $("#product_preview_container").offset().top - 40
                    }, 500);
                } else {
                    errorMsg.innerText = response.message || '{{ __("Unable to fetch product. Please check the link.") }}';
                    errorAlert.classList.remove('d-none');
                }
            },
            error: function(xhr) {
                fetchBtn.disabled = false;
                spinner.classList.add('d-none');
                btnText.innerHTML = '<i class="fas fa-cloud-download-alt mr-1"></i> {{ __("Fetch from Link") }}';

                var message = '{{ __("Failed to fetch product. Please make sure the link is accessible.") }}';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                errorMsg.innerText = message;
                errorAlert.classList.remove('d-none');

                if (xhr.responseJSON && xhr.responseJSON.is_hhc_member) {
                    $('#hhc-tab').tab('show');
                }
            }
        });
    }

    function populatePreviewForm(data) {
        // Name & Identity
        $('#imp_name').val(data.name || '');
        $('#imp_sku').val(data.sku || '{{ \App\Repositories\Back\ItemRepository::generateAutoSku() }}');
        $('#imp_sort_details').val(data.sort_details || '');
        $('#imp_details').val(data.details || '');
        $('#imp_product_from').val(data.product_from || 'HHC Dropshipping');
        $('#imp_supplier_url').val(data.supplier_url || '');
        if (data.stock) {
            $('#imp_stock').val(data.stock);
        }

        // SEO Tags & Meta Fields (Tagify with Cross Remove Buttons)
        setTagifyValue('#imp_tags', data.tags || '');
        setTagifyValue('#imp_meta_keywords', data.meta_keywords || '');
        $('#imp_meta_description').val(data.meta_description || '');

        // Price calculations
        var rawPrice = parseFloat(data.raw_price) || 0;
        if (rawPrice > 0) {
            $('#imp_cost_price').val(rawPrice);
            // Default markup: Cost + Rs. 600 or 2x
            var suggestedSell = Math.round(rawPrice + 600);
            $('#imp_discount_price').val(suggestedSell);
            $('#imp_previous_price').val(Math.round(suggestedSell * 1.35));
        } else {
            $('#imp_cost_price').val('');
            $('#imp_discount_price').val('');
            $('#imp_previous_price').val('');
        }
        calculateProfit();

        // Auto-select Matched Category
        if (data.category_id) {
            $('#imp_category_id').val(data.category_id);
            loadSubcategories(data.category_id);
        }

        // Auto-select Matched Brand
        if (data.brand_id) {
            $('#imp_brand_id').val(data.brand_id);
        } else {
            $('#imp_brand_id').val('');
        }

        // Populate Images Grid
        var grid = document.getElementById('images_grid_container');
        grid.innerHTML = '';

        if (data.images && data.images.length > 0) {
            $('#selected_main_image_url').val(data.images[0]);

            data.images.forEach(function(imgUrl, idx) {
                var isMain = (idx === 0);
                var labelText = isMain ? '{{ __("⭐ Main Featured") }}' : ('{{ __("Gallery") }} ' + idx);
                var col = document.createElement('div');
                col.className = 'col-6 col-md-4 col-lg-3 mb-3';
                col.innerHTML = `
                    <div class="image-picker-card ${isMain ? 'selected-main' : ''}" onclick="selectMainImage(this, '${escapeHtml(imgUrl)}')">
                        <span class="main-badge"><i class="fas fa-star mr-1"></i> Main Photo</span>
                        <span class="gallery-badge"><i class="fas fa-image mr-1"></i> ${labelText}</span>
                        <div class="gallery-checkbox-wrap" onclick="event.stopPropagation();">
                            <input type="checkbox" name="gallery_urls[]" value="${escapeHtml(imgUrl)}" ${isMain ? '' : 'checked'} title="{{ __('Include in Gallery') }}">
                        </div>
                        <img src="${escapeHtml(imgUrl)}" class="image-picker-img" alt="Product Image" onerror="this.onerror=null; this.src='{{ asset('assets/images/placeholder.png') }}';">
                    </div>
                `;
                grid.appendChild(col);
            });
        } else {
            grid.innerHTML = '<div class="col-12 text-muted text-center py-3"><i class="fas fa-image mr-1"></i> {{ __("No images found in text. You can paste image link above or upload photo files below.") }}</div>';
        }
    }

    function selectMainImage(cardEl, imgUrl) {
        document.querySelectorAll('.image-picker-card').forEach(function(el) {
            el.classList.remove('selected-main');
        });
        cardEl.classList.add('selected-main');
        document.getElementById('selected_main_image_url').value = imgUrl;
    }

    function calculateProfit() {
        var cost = parseFloat($('#imp_cost_price').val()) || 0;
        var sell = parseFloat($('#imp_discount_price').val()) || 0;
        var profit = 0;

        if (sell > 0) {
            profit = cost > 0 ? (sell - cost) : sell;
        }

        $('#imp_estimated_profit').val(profit);
        $('#estimated_profit_display').text('Rs ' + Math.round(profit).toLocaleString());
    }

    function loadSubcategories(catId, selectedSubId = null) {
        if (!catId) {
            $('#imp_subcategory_id').html('<option value="">{{ __("Select One") }}</option>');
            $('#imp_childcategory_id').html('<option value="">{{ __("Select One") }}</option>');
            return;
        }

        $.ajax({
            url: "{{ route('back.get.subcategory') }}",
            type: "GET",
            data: { category_id: catId },
            success: function(response) {
                var html = '<option value="">{{ __("Select One") }}</option>';
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(item) {
                        var sel = (selectedSubId && selectedSubId == item.id) ? 'selected' : '';
                        html += '<option value="' + item.id + '" ' + sel + '>' + item.name + '</option>';
                    });
                }
                $('#imp_subcategory_id').html(html);
                $('#imp_childcategory_id').html('<option value="">{{ __("Select One") }}</option>');
            }
        });
    }

    function loadChildCategories(subId, selectedChildId = null) {
        if (!subId) {
            $('#imp_childcategory_id').html('<option value="">{{ __("Select One") }}</option>');
            return;
        }

        $.ajax({
            url: "{{ route('back.get.childcategory') }}",
            type: "GET",
            data: { subcategory_id: subId },
            success: function(response) {
                var html = '<option value="">{{ __("Select One") }}</option>';
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(item) {
                        var sel = (selectedChildId && selectedChildId == item.id) ? 'selected' : '';
                        html += '<option value="' + item.id + '" ' + sel + '>' + item.name + '</option>';
                    });
                }
                $('#imp_childcategory_id').html(html);
            }
        });
    }

    function setTagifyValue(selector, value) {
        var $input = $(selector);
        if (!$input.length) return;

        var instance = $input.data('tagify');
        if (!instance) {
            $input.tagify();
            instance = $input.data('tagify');
        }

        if (instance) {
            instance.removeAllTags();
            if (value && typeof value === 'string' && value.trim().length > 0) {
                instance.addTags(value.trim());
            }
        } else {
            $input.val(value || '');
        }
    }

    // Specifications
    function toggleSpecificationSection(el) {
        if (el.checked) {
            $('#specifications-section').slideDown();
        } else {
            $('#specifications-section').slideUp();
        }
    }

    $(document).on('click', '.add-specification', function () {
        var text = $(this).data('text') || '{{ __("Specification Name") }}';
        var text1 = $(this).data('text1') || '{{ __("Specification Description") }}';
        $('#specifications-section').append(`
            <div class="d-flex mb-2">
                <div class="flex-grow-1 mr-2">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control" name="specification_name[]" placeholder="${text}">
                    </div>
                </div>
                <div class="flex-grow-1 mr-2">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control" name="specification_description[]" placeholder="${text1}">
                    </div>
                </div>
                <div class="flex-btn">
                    <button type="button" class="btn btn-danger remove-spcification remove-specification">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        `);
    });

    $(document).on('click', '.remove-specification', function () {
        $(this).closest('.d-flex').remove();
    });

    // Variants Management
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
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeVariantRow(this)" title="{{ __('Delete') }}"><i class="fas fa-trash"></i></button>
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
            alert('{{ __("Please enter at least one Color or Size.") }}');
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
        var isVariantChecked = document.getElementById('is_variant_toggle') && document.getElementById('is_variant_toggle').checked;
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

        var mainStockInput = document.getElementById('imp_stock');
        if (mainStockInput && stockInputs.length > 0) {
            mainStockInput.value = total;
        }
    }

    // Rating Management Functions
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
                            <textarea name="demo_review[]" class="form-control form-control-sm" rows="2" placeholder="e.g. Ordered this and loved the quality!" required>${escapeHtml(review)}</textarea>
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

    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }
</script>
@endsection
