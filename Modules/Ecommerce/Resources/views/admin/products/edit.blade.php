@extends('admin.master_layout')
@section('title')
    <title>{{ __('translate.Edit Product') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Edit Product') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Manage Product') }} >> {{ __('translate.Edit Product') }}</p>
@endsection

@section('body-content')

<section class="crancy-adashboard crancy-show">
    <div class="container container__bscreen">
        <div class="row">
            <div class="col-12">
                <div class="crancy-body">
                    <div class="crancy-dsinner">
                        <div class="row">
                            <div class="col-12 mg-top-30">
                                <div class="crancy-product-card translation_main_box">
                                    <div class="crancy-customer-filter">
                                        <div class="crancy-customer-filter__single crancy-customer-filter__single--csearch">
                                            <div class="crancy-header__form crancy-header__form--customer">
                                                <h4 class="crancy-product-card__title">{{ __('translate.Switch to language translation') }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="translation_box">
                                        <ul>
                                            @foreach ($language_list as $language)
                                            <li><a href="{{ route('admin.product.edit', ['product_id' => $product->id, 'lang_code' => $language->lang_code] ) }}">
                                                @if (request()->get('lang_code') == $language->lang_code)
                                                    <i class="fas fa-eye"></i>
                                                @else
                                                    <i class="fas fa-edit"></i>
                                                @endif
                                                {{ $language->lang_name }}</a></li>
                                            @endforeach
                                        </ul>
                                        <div class="alert alert-secondary" role="alert">
                                            @php
                                                $edited_language = $language_list->where('lang_code', request()->get('lang_code'))->first();
                                            @endphp
                                            <p>{{ __('translate.Your editing mode') }} : <b>{{ $edited_language->lang_name }}</b></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<form action="{{ route('admin.product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <input type="hidden" name="lang_code" value="{{ $listing_translate->lang_code }}">
    <input type="hidden" name="translate_id" value="{{ $listing_translate->id }}">

    <section class="crancy-adashboard crancy-show">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <div class="crancy-dsinner">
                            <div class="row">
                                <div class="col-12 mg-top-30">
                                    <div class="crancy-product-card">
                                        <div class="create_new_btn_inline_box">
                                            <h4 class="crancy-product-card__title">{{ __('translate.Basic Information') }}</h4>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 mg-top-form-20">
                                                <div class="row">
                                                    @if (admin_lang() == request()->get('lang_code'))
                                                        <div class="col-md-3">
                                                            <div class="crancy__item-form--group w-100 h-100">
                                                                <label class="crancy__item-label">{{ __('translate.Thumbnail Image') }} * </label>
                                                                <div class="crancy-product-card__upload crancy-product-card__upload--border">
                                                                    <input type="file" class="btn-check" name="thumbnail_image" id="input-img1" autocomplete="off" onchange="previewImage(event)">
                                                                    <label class="crancy-image-video-upload__label" for="input-img1">
                                                                        <img id="view_img" src="{{ $product->thumbnail_image ? asset($product->thumbnail_image) : asset($general_setting->placeholder_image) }}">
                                                                        <h4 class="crancy-image-video-upload__title">{{ __('translate.Click here to') }}
                                                                            <span class="crancy-primary-color">{{ __('translate.Choose File') }}</span> {{ __('translate.and upload') }}
                                                                        </h4>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="col-md-9">
                                                        <div class="crancy__item-form--group mg-top-form-20">
                                                            <label class="crancy__item-label">{{ __('translate.Title') }} * </label>
                                                            <input class="crancy__item-input" type="text" name="name" id="name" value="{{ old('name', $listing_translate->name ?? '') }}">
                                                        </div>
                                                        @if (admin_lang() == request()->get('lang_code'))
                                                            <div class="crancy__item-form--group mg-top-form-20">
                                                                <label class="crancy__item-label">{{ __('translate.Slug') }} *</label>
                                                                <input class="crancy__item-input" type="text" name="slug" id="slug" value="{{ old('slug', $product->slug ?? '') }}">
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            @if (admin_lang() == request()->get('lang_code'))
                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label class="crancy__item-label">{{ __('translate.Price') }} *</label>
                                                        <input class="crancy__item-input" type="number" step="0.01" name="price" id="price" value="{{ old('price', $product->price ?? '') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label class="crancy__item-label">{{ __('translate.Brand') }} </label>
                                                        <select class="form-select crancy__item-input" name="brand_id" id="brand_id">
                                                            <option value="" selected disabled>{{ __('translate.Select Brand') }}</option>
                                                            @foreach ($brands as $brand)
                                                                <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->translate->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label class="crancy__item-label">{{ __('translate.Category') }} * </label>
                                                        <select class="form-select crancy__item-input" name="category_id" id="category_id">
                                                            <option value="" selected disabled>{{ __('translate.Select Category') }}</option>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->translate->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="crancy__item-form--group mg-top-form-20">
                                                        <label class="crancy__item-label">{{ __('translate.Tags') }} </label>
                                                        <input class="crancy__item-input tags" type="text" name="tags" value="{{ old('tags', $product->tags) }}">
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-12">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <label class="crancy__item-label">{{ __('translate.Short Description') }} </label>
                                                    <textarea class="crancy__item-input crancy__item-textarea seo_description_box" name="short_description" id="short_description">{{ old('short_description', $listing_translate->short_description ?? '') }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <label class="crancy__item-label">{{ __('translate.Description') }} *</label>
                                                    <textarea class="crancy__item-input crancy__item-textarea summernote" name="description" id="description">{{ old('description', $listing_translate->description ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if (admin_lang() == request()->get('lang_code'))
    <section class="crancy-adashboard crancy-show">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <div class="crancy-dsinner">
                            <div class="row">
                                <div class="col-12">
                                    <div class="crancy-product-card">
                                        <div class="create_new_btn_inline_box">
                                            <h4 class="crancy-product-card__title">{{ __('translate.Product Type') }}</h4>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <div class="crancy-radio-group" style="display:flex;gap:24px;flex-wrap:wrap">
                                                        <label class="crancy-radio-label" style="display:flex;align-items:center;gap:8px;padding:14px 24px;border:2px solid #e0e3e8;border-radius:10px;cursor:pointer;transition:all 0.15s">
                                                            <input type="radio" name="product_type" value="physical" {{ old('product_type', $product->product_type ?? 'physical') == 'physical' ? 'checked' : '' }} onchange="toggleProductType(this.value)" style="width:18px;height:18px;accent-color:#2b4dff">
                                                            <span>
                                                                <strong>{{ __('translate.Physical Product') }}</strong>
                                                                <br><small class="text-muted">{{ __('translate.Shippable item with inventory') }}</small>
                                                            </span>
                                                        </label>
                                                        <label class="crancy-radio-label" style="display:flex;align-items:center;gap:8px;padding:14px 24px;border:2px solid #e0e3e8;border-radius:10px;cursor:pointer;transition:all 0.15s">
                                                            <input type="radio" name="product_type" value="script" {{ old('product_type', $product->product_type ?? '') == 'script' ? 'checked' : '' }} onchange="toggleProductType(this.value)" style="width:18px;height:18px;accent-color:#2b4dff">
                                                            <span>
                                                                <strong>{{ __('translate.Script / Code') }}</strong>
                                                                <br><small class="text-muted">{{ __('translate.Digital download with licensing & updates') }}</small>
                                                            </span>
                                                        </label>
                                                        <label class="crancy-radio-label" style="display:flex;align-items:center;gap:8px;padding:14px 24px;border:2px solid #e0e3e8;border-radius:10px;cursor:pointer;transition:all 0.15s">
                                                            <input type="radio" name="product_type" value="ebook" {{ old('product_type', $product->product_type ?? '') == 'ebook' ? 'checked' : '' }} onchange="toggleProductType(this.value)" style="width:18px;height:18px;accent-color:#2b4dff">
                                                            <span>
                                                                <strong>{{ __('translate.eBook / PDF') }}</strong>
                                                                <br><small class="text-muted">{{ __('translate.Simple file download, no licensing') }}</small>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="crancy-adashboard crancy-show" id="physicalSettings" style="display:none">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <div class="crancy-dsinner">
                            <div class="row">
                                <div class="col-12">
                                    <div class="crancy-product-card">
                                        <div class="create_new_btn_inline_box">
                                            <h4 class="crancy-product-card__title">{{ __('translate.Physical Product Settings') }}</h4>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <label class="crancy__item-label">{{ __('translate.Offer Price (%)') }}</label>
                                                    <div class="input-group">
                                                        <input class="crancy__item-input form-control offer-price" type="number" name="offer_price" id="offer_price" value="{{ old('offer_price', $product->offer_price ?? '') }}" min="0" max="100">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="crancy-adashboard crancy-show" id="scriptSettings" style="display:none">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <div class="crancy-dsinner">
                            <div class="row">
                                <div class="col-12">
                                    <div class="crancy-product-card">
                                        <div class="create_new_btn_inline_box">
                                            <h4 class="crancy-product-card__title">{{ __('translate.Script / Code Settings') }}</h4>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <label class="crancy__item-label">{{ __('translate.License Type') }}</label>
                                                    <select class="form-select crancy__item-input" name="license_type" id="license_type">
                                                        <option value="none" {{ old('license_type', $product->license_type ?? '') == 'none' ? 'selected' : '' }}>{{ __('translate.None (No License)') }}</option>
                                                        <option value="regular" {{ old('license_type', $product->license_type ?? '') == 'regular' ? 'selected' : '' }}>{{ __('translate.Regular License') }}</option>
                                                        <option value="extended" {{ old('license_type', $product->license_type ?? '') == 'extended' ? 'selected' : '' }}>{{ __('translate.Extended License') }}</option>
                                                        <option value="both" {{ old('license_type', $product->license_type ?? '') == 'both' ? 'selected' : '' }}>{{ __('translate.Both Regular & Extended') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <label class="crancy__item-label">{{ __('translate.Regular Price') }}</label>
                                                    <input class="crancy__item-input" type="number" step="0.01" name="regular_price" id="regular_price" value="{{ old('regular_price', $product->regular_price ?? '') }}" placeholder="{{ __('translate.Price for regular license') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <label class="crancy__item-label">{{ __('translate.Extended Price') }}</label>
                                                    <input class="crancy__item-input" type="number" step="0.01" name="extended_price" id="extended_price" value="{{ old('extended_price', $product->extended_price ?? '') }}" placeholder="{{ __('translate.Price for extended license') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <label class="crancy__item-label">{{ __('translate.Demo URL') }}</label>
                                                    <input class="crancy__item-input" type="url" name="demo_url" id="demo_url" value="{{ old('demo_url', $product->demo_url ?? '') }}" placeholder="https://...">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <label class="crancy__item-label">{{ __('translate.Download Limit') }}</label>
                                                    <input class="crancy__item-input" type="number" min="1" name="download_limit" id="script_download_limit" value="{{ old('download_limit', $product->download_limit ?? 5) }}" placeholder="{{ __('translate.Max downloads per purchase') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <label class="crancy__item-label">{{ __('translate.Update Support (Months)') }}</label>
                                                    <input class="crancy__item-input" type="number" min="0" max="120" name="update_support_months" id="update_support_months" value="{{ old('update_support_months', $product->update_support_months ?? 6) }}" placeholder="{{ __('translate.0 = No support') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <label class="crancy__item-label">{{ __('translate.File Access') }}</label>
                                                    <select class="form-select crancy__item-input" name="file_access" id="file_access">
                                                        <option value="order" {{ old('file_access', $product->file_access ?? 'order') == 'order' ? 'selected' : '' }}>{{ __('translate.Authenticated (Order-Based)') }}</option>
                                                        <option value="email" {{ old('file_access', $product->file_access ?? '') == 'email' ? 'selected' : '' }}>{{ __('translate.Email Link Only') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            @if($product->currentFile)
                                            <div class="col-12">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <div class="alert alert-info">
                                                        <strong>{{ __('translate.Current File') }}:</strong> {{ $product->currentFile->file_name }}
                                                        (v{{ $product->currentFile->version }}, {{ number_format($product->currentFile->file_size / 1024 / 1024, 2) }} MB)
                                                        <br><small>{{ __('translate.Upload a new file below to replace it') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="col-12">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <label class="crancy__item-label">{{ __('translate.Product File (ZIP)') }}</label>
                                                    <div class="crancy-file-upload-box" style="border:2px dashed #ccd0d5;border-radius:10px;padding:24px;text-align:center;background:#fafbfc">
                                                        <input type="file" name="product_file" id="scriptProductFile" accept=".zip,application/zip,application/x-zip-compressed" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0" onchange="updateFileName(this, 'scriptFileLabel')">
                                                        <label for="scriptProductFile" style="cursor:pointer;display:block">
                                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                                            <p style="margin:8px 0 0;font-size:14px;color:#6b7280"><strong>{{ __('translate.Click to upload') }}</strong> {{ __('translate.or drag and drop') }}</p>
                                                            <p style="margin:4px 0 0;font-size:12px;color:#9ca3af">{{ __('translate.ZIP files only, max 512MB') }}</p>
                                                            <p id="scriptFileLabel" style="margin:8px 0 0;font-size:13px;color:#2b4dff;font-weight:600"></p>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <label class="crancy__item-label">{{ __('translate.File Version') }}</label>
                                                    <input class="crancy__item-input" type="text" name="file_version" id="file_version" value="{{ old('file_version', '1.0.0') }}" placeholder="e.g. 1.0.0">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <label class="crancy__item-label">{{ __('translate.Changelog') }}</label>
                                                    <textarea class="crancy__item-input crancy__item-textarea" name="file_changelog" id="file_changelog" rows="3" placeholder="{{ __('translate.Describe what this version includes') }}">{{ old('file_changelog') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="crancy-adashboard crancy-show" id="ebookSettings" style="display:none">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <div class="crancy-dsinner">
                            <div class="row">
                                <div class="col-12">
                                    <div class="crancy-product-card">
                                        <div class="create_new_btn_inline_box">
                                            <h4 class="crancy-product-card__title">{{ __('translate.eBook / PDF Settings') }}</h4>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <label class="crancy__item-label">{{ __('translate.Download Limit') }}</label>
                                                    <input class="crancy__item-input" type="number" min="1" name="download_limit" id="ebook_download_limit" value="{{ old('download_limit', $product->download_limit ?? 5) }}" placeholder="{{ __('translate.Max downloads per purchase') }}">
                                                </div>
                                            </div>
                                            @if($product->currentFile)
                                            <div class="col-12">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <div class="alert alert-info">
                                                        <strong>{{ __('translate.Current File') }}:</strong> {{ $product->currentFile->file_name }}
                                                        ({{ number_format($product->currentFile->file_size / 1024 / 1024, 2) }} MB)
                                                        <br><small>{{ __('translate.Upload a new file below to replace it') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="col-12">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <label class="crancy__item-label">{{ __('translate.Product File (PDF / ZIP / EPUB)') }}</label>
                                                    <div class="crancy-file-upload-box" style="border:2px dashed #ccd0d5;border-radius:10px;padding:24px;text-align:center;background:#fafbfc">
                                                        <input type="file" name="product_file" id="ebookProductFile" accept=".pdf,.zip,.epub,application/pdf,application/zip,application/epub+zip" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0" onchange="updateFileName(this, 'ebookFileLabel')">
                                                        <label for="ebookProductFile" style="cursor:pointer;display:block">
                                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                                            <p style="margin:8px 0 0;font-size:14px;color:#6b7280"><strong>{{ __('translate.Click to upload') }}</strong> {{ __('translate.or drag and drop') }}</p>
                                                            <p style="margin:4px 0 0;font-size:12px;color:#9ca3af">{{ __('translate.PDF, EPUB or ZIP files, max 512MB') }}</p>
                                                            <p id="ebookFileLabel" style="margin:8px 0 0;font-size:13px;color:#2b4dff;font-weight:600"></p>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <section class="crancy-adashboard crancy-show">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <div class="crancy-dsinner">
                            <div class="row">
                                <div class="col-12">
                                    <div class="crancy-product-card">
                                        <div class="create_new_btn_inline_box">
                                            <h4 class="crancy-product-card__title">{{ __('translate.SEO Information') }}</h4>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <label class="crancy__item-label">{{ __('translate.SEO title') }} </label>
                                                    <input class="crancy__item-input" type="text" name="seo_title" id="seo_title" value="{{ old('seo_title', $listing_translate->seo_title ?? '') }}">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <label class="crancy__item-label">{{ __('translate.SEO Description') }} </label>
                                                    <textarea class="crancy__item-input crancy__item-textarea seo_description_box" name="seo_description" id="seo_description">{{ old('seo_description', $listing_translate->seo_description ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="crancy-btn mg-top-25" type="submit">{{ __('translate.Update Data') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</form>
@endsection

@push('style_section')
    <link rel="stylesheet" href="{{ asset('global/tagify/tagify.css') }}">
    <style>
        .tox .tox-promotion, .tox-statusbar__branding { display: none !important; }
        .crancy-radio-label:hover { border-color: #2b4dff !important; background: #f5f7ff; }
        .crancy-radio-label:has(input:checked) { border-color: #2b4dff !important; background: #f0f4ff; }
    </style>
@endpush

@push('js_section')
    <script src="{{ asset('global/tagify/tagify.js') }}"></script>
    <script src="{{ asset('global/tinymce/js/tinymce/tinymce.min.js') }}"></script>
    <script>
        (function ($) {
            "use strict"
            $(document).ready(function () {
                $("#name").on("keyup", function (e) {
                    let inputValue = $(this).val();
                    let slug = inputValue.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
                    $("#slug").val(slug);
                });

                tinymce.init({
                    selector: '.summernote',
                    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
                    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
                    tinycomments_mode: 'embedded',
                    tinycomments_author: 'Author name',
                    mergetags_list: [
                        {value: 'First.Name', title: 'First Name'},
                        {value: 'Email', title: 'Email'},
                    ]
                });
                $('.tags').tagify();

                var selectedType = $('input[name="product_type"]:checked').val();
                if (selectedType) {
                    toggleProductType(selectedType);
                }
            });
        })(jQuery);

        function toggleProductType(type) {
    document.getElementById('physicalSettings').style.display = type === 'physical' ? 'block' : 'none';
    document.getElementById('scriptSettings').style.display = type === 'script' ? 'block' : 'none';
    document.getElementById('ebookSettings').style.display = type === 'ebook' ? 'block' : 'none';

    // Disable file inputs that aren't part of the active product type so they
    // don't get submitted alongside (and overwrite) the active one — both
    // inputs share name="product_file", and hidden inputs are still POSTed.
    var scriptFileInput = document.getElementById('scriptProductFile');
    var ebookFileInput = document.getElementById('ebookProductFile');
    if (scriptFileInput) scriptFileInput.disabled = (type !== 'script');
    if (ebookFileInput) ebookFileInput.disabled = (type !== 'ebook');

    var radioLabels = document.querySelectorAll('.crancy-radio-label');
    radioLabels.forEach(function(l) {
        l.style.borderColor = '#e0e3e8';
        l.style.background = '#fff';
        var input = l.querySelector('input');
        if (input && input.checked) {
            l.style.borderColor = '#2b4dff';
            l.style.background = '#f0f4ff';
        }
    });
}

        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function () {
                var output = document.getElementById('view_img');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        function updateFileName(input, labelId) {
            var label = document.getElementById(labelId);
            if (input.files && input.files.length > 0) {
                label.textContent = input.files[0].name + ' (' + (input.files[0].size / 1024 / 1024).toFixed(2) + ' MB)';
            } else {
                label.textContent = '';
            }
        }
    </script>
@endpush