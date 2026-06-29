@extends('master_layout')
@section('new-layout')

<div class="optech-breadcrumb" style="background-image: url({{ asset($general_setting->breadcrumb_image) }})">
    <div class="container">
        <h1 class="post__title">{{ isset($pageTitle) ? __($pageTitle) : __('translate.Marketplace') }}</h1>
        <nav class="breadcrumbs">
            <ul>
                <li><a href="{{ route('home') }}">{{ __('translate.Home') }}</a></li>
                <li aria-current="page">{{ isset($pageTitle) ? __($pageTitle) : __('translate.Marketplace') }}</li>
            </ul>
        </nav>
    </div>
</div>

<div class="section optech-section-padding">
    <div class="container">
        <div class="row">
            @include('frontend.shop.sidebar_search')

            @if($products->count() > 0)
                <div class="col-xl-9 col-lg-8 col-md-7">
                    <div class="cc-toolbar">
                        <p class="cc-result-count">{{ $products->total() }} {{ __('translate.items') }}</p>
                        <div class="cc-sort">
                            <select onchange="window.location.href=this.value" class="form-select form-select-sm">
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort') == 'newest' || !request('sort') ? 'selected' : '' }}>{{ __('translate.Newest Items') }}</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'sales']) }}" {{ request('sort') == 'sales' ? 'selected' : '' }}>{{ __('translate.Best Selling') }}</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'rating']) }}" {{ request('sort') == 'rating' ? 'selected' : '' }}>{{ __('translate.Top Rated') }}</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>{{ __('translate.Price: Low to High') }}</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>{{ __('translate.Price: High to Low') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row cc-items-grid">
                        @foreach($products as $product)
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                @include('_product')
                            </div>
                        @endforeach
                    </div>
                    @include('frontend.shop.paginate')
                </div>
            @else
                <div class="col-xl-9 col-lg-8 col-md-7">
                    @include('frontend.shop.not_found')
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.cc-toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid #eef0f4}
.cc-result-count{font-size:14px;color:#666;margin:0}
.cc-sort select{min-width:180px;padding:6px 12px;border:1px solid #dde0e4;border-radius:6px;font-size:13px;color:#333;background:#fff}
.codecanyon-item{background:#fff;border-radius:8px;overflow:hidden;border:1px solid #eef0f4;transition:box-shadow .2s;margin-bottom:24px}
.codecanyon-item:hover{box-shadow:0 4px 20px rgba(0,0,0,.08)}
.cc-item-thumb{position:relative;background:#f7f8fa;text-align:center;padding:0;overflow:hidden}
.cc-item-thumb img{width:100%;height:180px;object-fit:cover;display:block}
.cc-live-preview{position:absolute;top:10px;left:10px;background:rgba(0,0,0,.7);color:#fff;font-size:11px;padding:4px 10px;border-radius:4px;text-decoration:none;display:inline-flex;align-items:center;gap:5px;opacity:0;transition:opacity .2s}
.codecanyon-item:hover .cc-live-preview{opacity:1}
.cc-live-preview:hover{background:#2b4dff}
.cc-item-body{padding:14px 16px 12px}
.cc-item-type{margin-bottom:6px}
.cc-mini-type{font-size:10px;padding:2px 8px;border-radius:8px;font-weight:600;text-transform:uppercase;letter-spacing:.3px}
.cc-mini-type.cc-type-physical{background:#e8f5e9;color:#2e7d32}
.cc-mini-type.cc-type-script{background:#e3f2fd;color:#1565c0}
.cc-mini-type.cc-type-ebook{background:#fce4ec;color:#c62828}
.cc-item-title{margin:0 0 6px;font-size:15px;line-height:1.3}
.cc-item-title a{color:#1a1a2e;text-decoration:none;font-weight:600}
.cc-item-title a:hover{color:#2b4dff}
.cc-item-meta{margin-bottom:10px}
.cc-rating{display:flex;align-items:center;gap:6px}
.cc-stars{display:inline-flex;gap:1px}
.star-filled{fill:#ffb400;stroke:#ffb400}
.star-empty{fill:transparent;stroke:#d0d5dd}
.cc-rating-count{font-size:12px;color:#888}
.cc-item-footer{display:flex;align-items:center;justify-content:space-between;padding-top:10px;border-top:1px solid #f0f2f5}
.cc-price{font-size:16px;font-weight:700;color:#1a1a2e}
.cc-actions{display:flex;gap:6px}
.cc-cart-btn{width:34px;height:34px;border-radius:6px;border:1px solid #e0e3e8;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#555;transition:all .15s}
.cc-cart-btn:hover{background:#2b4dff;border-color:#2b4dff;color:#fff}
.cc-wishlist{width:34px;height:34px;border-radius:6px;border:1px solid #e0e3e8;display:flex;align-items:center;justify-content:center;color:#bbb;transition:all .15s;text-decoration:none}
.cc-wishlist:hover{color:#e74c3c;border-color:#e74c3c}
.cc-wishlist.active{color:#e74c3c;border-color:#e74c3c;background:#fff5f5}
@media(max-width:767px){.cc-toolbar{flex-direction:column;gap:10px;align-items:flex-start}}
</style>

@endsection
