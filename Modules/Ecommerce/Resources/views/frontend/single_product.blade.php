@extends('master_layout')
@section('title')
    <title>{{ $product->translate?->seo_title }}</title>
    <meta name="title" content="{{ $product->translate?->seo_title }}">
    <meta name="description" content="{!! strip_tags(clean($product->translate?->seo_description)) !!}">
@endsection

@section('new-layout')
    <div class="optech-breadcrumb" style="background-image: url({{ asset($general_setting->breadcrumb_image)  }})">
        <div class="container">
            <h1 class="post__title">{{ __($pageTitle) }}</h1>
            <nav class="breadcrumbs">
                <ul>
                    <li><a href="{{ route('home') }}">{{ __('translate.Home') }}</a></li>
                    <li><a href="{{ route('product.shop') }}">{{ __('translate.Marketplace') }}</a></li>
                    <li aria-current="page">{{ __($pageTitle) }}</li>
                </ul>
            </nav>
        </div>
    </div>

    <div class="section optech-section-padding-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="cc-product-header">
                        <h1 class="cc-product-title">{{ html_decode($pageTitle) }}</h1>
                        <div class="cc-product-meta">
                            @php $avgRating = $product->reviews->avg('rating') ?? 0; $reviewCount = $product->reviews->count(); @endphp
                            <div class="cc-rating-large">
                                <div class="cc-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="{{ $i <= round($avgRating) ? 'star-filled' : 'star-empty' }}" width="18" height="18" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    @endfor
                                </div>
                                <span class="cc-rating-value">{{ number_format($avgRating, 1) }}</span>
                                <span class="cc-rating-count">({{ $reviewCount }} {{ __('translate.reviews') }})</span>
                            </div>
                            @if($product->category)
                                <span class="cc-category-badge">{{ $product->category?->translate->name }}</span>
                            @endif
                            <span class="cc-type-badge cc-type-{{ $product->product_type }}">{{ __('translate.' . ($product->product_type === 'script' ? 'Script / Code' : ($product->product_type === 'ebook' ? 'eBook / PDF' : 'Physical'))) }}</span>
                        </div>
                        <div class="cc-product-dates">
                            <span>{{ __('translate.Created') }}: {{ $product->created_at->format('M d, Y') }}</span>
                            @if($product->updated_at && $product->updated_at->ne($product->created_at))
                                <span class="cc-sep">|</span>
                                <span>{{ __('translate.Last Updated') }}: {{ $product->updated_at->format('M d, Y') }}</span>
                            @endif
                            @if($product->isScript() && $product->currentFile)
                                <span class="cc-sep">|</span>
                                <span>{{ __('translate.Version') }}: {{ $product->currentFile->version }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="cc-preview-wrap">
                        @if($product->isPhysical() && $product->galleries->count() > 0)
                            <div class="cc-main-preview">
                                <img src="{{ asset($product->galleries->first()->image) }}" alt="{{ $pageTitle }}" id="mainPreview">
                            </div>
                            @if($product->galleries->count() > 1)
                                <div class="cc-thumb-strip">
                                    @foreach($product->galleries as $gallery)
                                        <div class="cc-thumb-item {{ $loop->first ? 'active' : '' }}" onclick="swapPreview(this, '{{ asset($gallery->image) }}')">
                                            <img src="{{ asset($gallery->image) }}" alt="">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="cc-main-preview">
                                <img src="{{ asset($product->thumbnail_image) }}" alt="{{ $pageTitle }}" id="mainPreview">
                            </div>
                        @endif
                    </div>

                    @if($product->isScript() && $product->currentFile)
                    <div class="cc-version-section">
                        <div class="cc-version-header">
                            <span class="cc-version-badge-lg">v{{ $product->currentFile->version }}</span>
                            <span class="cc-version-label">{{ __('translate.Current Version') }}</span>
                            @if($product->currentFile->file_size)
                            <span class="cc-version-meta">{{ number_format($product->currentFile->file_size / 1024 / 1024, 2) }} MB</span>
                            @endif
                            <span class="cc-version-date">{{ __('translate.Released') }}: {{ $product->currentFile->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($product->currentFile->changelog)
                        <div class="cc-version-changelog">
                            <strong>{{ __('translate.What\'s Changed') }}</strong>
                            <div class="cc-version-changelog-body">{!! nl2br(e($product->currentFile->changelog)) !!}</div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <div class="cc-tabs-wrap">
                        <ul class="nav nav-pills cc-tabs" id="productTabs">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#descTab">{{ __('translate.Description') }}</button>
                            </li>
                            @if($product->isScript() && $product->updates->count() > 0)
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#changelogTab">{{ __('translate.Changelog') }} ({{ $product->updates->count() }})</button>
                                </li>
                            @endif
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#reviewTab">{{ __('translate.Reviews') }} ({{ $reviewCount }})</button>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="descTab">
                                <div class="entry-content">{!! clean($product->translate?->description) !!}</div>
                            </div>
                            @if($product->isScript() && $product->updates->count() > 0)
                                <div class="tab-pane fade" id="changelogTab">
                                    <div class="cc-changelog-list">
                                        @foreach($product->updates->sortByDesc('created_at') as $update)
                                            <div class="cc-changelog-item">
                                                <div class="cc-changelog-version">
                                                    <span class="cc-version-badge">v{{ $update->version }}</span>
                                                    <span class="cc-changelog-date">{{ $update->created_at->format('M d, Y') }}</span>
                                                </div>
                                                <div class="cc-changelog-body">
                                                    {!! nl2br(e($update->changelog)) !!}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <div class="tab-pane fade" id="reviewTab">
                                @forelse($reviews as $review)
                                    <div class="cc-review-item">
                                        <div class="cc-review-avatar">
                                            @if($review->user && $review->user->image)
                                                <img src="{{ asset($review->user->image) }}" alt="">
                                            @else
                                                <img src="{{ asset($general_setting->default_avatar) }}" alt="">
                                            @endif
                                        </div>
                                        <div class="cc-review-body">
                                            <div class="cc-review-name">{{ $review->user?->name ?? __('translate.Anonymous') }}</div>
                                            <div class="cc-review-stars">
                                                @for($i = 0; $i < $review->rating; $i++) <i class="fa fa-star text-warning"></i> @endfor
                                            </div>
                                            <p>{{ html_decode($review->reviews) }}</p>
                                            <span class="cc-review-date">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted">{{ __('translate.No reviews yet') }}</p>
                                @endforelse

                                @if(auth()->user())
                                    <div class="cc-review-form mt-4">
                                        <h5>{{ __('translate.Write Your Review') }}</h5>
                                        <form method="POST" action="{{ route('user-order.reviewSubmit') }}">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="rating" id="product_rating" value="0">
                                            <div class="mb-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fa fa-star listing_rat fs-5" data-rating="{{ $i }}" onclick="listingReview({{ $i }})" style="cursor:pointer;color:#ddd"></i>
                                                @endfor
                                                <span id="rating_visible" class="ms-2 text-muted">(0.0)</span>
                                            </div>
                                            <textarea name="reviews" class="form-control mb-2" rows="3" required placeholder="{{ __('translate.Write your message') }}"></textarea>
                                            <button type="submit" class="optech-default-btn"><span>{{ __('translate.Submit Review') }}</span></button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="cc-sidebar-card">
                        <div class="cc-price-box">
                            <div class="cc-price-amount">{!! $product->price_display !!}</div>
                        </div>

                        <div class="cc-sidebar-actions">
                            <button class="cc-btn-primary cart-add-btn" data-product-id="{{ $product->id }}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                                {{ __('translate.Add to Cart') }}
                            </button>
                            @if($product->is_digital && $product->demo_url)
                                <a href="{{ $product->demo_url }}" target="_blank" rel="noopener" class="cc-btn-secondary">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                    {{ __('translate.Live Preview') }}
                                </a>
                            @endif
                            <a href="javascript:void(0)"
                               class="cc-btn-wishlist {{ auth()->check() && in_array($product->id, auth()->user()->wishlists->pluck('product_id')->toArray()) ? 'active' : '' }}"
                               data-url="{{ route('user.wishlist.store') }}"
                               onclick="addToWishlist({{ $product->id }}, this)">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                {{ __('translate.Add to Wishlist') }}
                            </a>
                        </div>

                        <div class="cc-sidebar-info">
                            @if($product->is_digital)
                                <div class="cc-info-row">
                                    <span class="cc-info-label">{{ __('translate.License') }}</span>
                                    <span class="cc-info-value">
                                        @if($product->license_type === 'none')
                                            {{ __('translate.No License Required') }}
                                        @elseif($product->license_type === 'both')
                                            {{ __('translate.Regular or Extended License') }}
                                        @else
                                            {{ ucfirst($product->license_type) }} {{ __('translate.License') }}
                                        @endif
                                    </span>
                                </div>
                                @if($product->demo_url)
                                <div class="cc-info-row">
                                    <span class="cc-info-label">{{ __('translate.Demo') }}</span>
                                    <span class="cc-info-value"><a href="{{ $product->demo_url }}" target="_blank">{{ __('translate.View Demo') }}</a></span>
                                </div>
                                @endif
                                @if($product->isScript() && $product->currentFile)
                                <div class="cc-info-row">
                                    <span class="cc-info-label">{{ __('translate.Version') }}</span>
                                    <span class="cc-info-value">v{{ $product->currentFile->version }}</span>
                                </div>
                                @if($product->currentFile->file_size)
                                <div class="cc-info-row">
                                    <span class="cc-info-label">{{ __('translate.File Size') }}</span>
                                    <span class="cc-info-value">{{ number_format($product->currentFile->file_size / 1024 / 1024, 2) }} MB</span>
                                </div>
                                @endif
                                @endif
                                @if($product->update_support_months && $product->update_support_months > 0)
                                <div class="cc-info-row">
                                    <span class="cc-info-label">{{ __('translate.Support') }}</span>
                                    <span class="cc-info-value">{{ $product->update_support_months }} {{ __('translate.months') }}</span>
                                </div>
                                @endif
                            @endif
                            <div class="cc-info-row">
                                <span class="cc-info-label">{{ __('translate.Category') }}</span>
                                <span class="cc-info-value">{{ $product->category?->translate->name }}</span>
                            </div>
                            <div class="cc-info-row">
                                <span class="cc-info-label">{{ __('translate.Tags') }}</span>
                                <span class="cc-info-value">
                                    @php
                                        $tags = '';
                                        if($product->tags){
                                            foreach (json_decode(html_decode($product->tags)) as $key => $service_tag) {
                                                $tags .= $service_tag->value.', ';
                                            }
                                        }
                                    @endphp
                                    {{ rtrim($tags, ', ') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($relatedProducts->count() > 0)
    <div class="section optech-section-padding">
        <div class="container">
            <h2 class="cc-section-title">{{ __('translate.Related Items') }}</h2>
            <div class="row">
                @foreach($relatedProducts as $relateProduct)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        @php $product = $relateProduct; @endphp
                        @include('_product')
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

<style>
.cc-product-header{margin-bottom:16px}
.cc-product-title{font-size:24px;font-weight:700;color:#1a1a2e;margin:0 0 10px}
.cc-product-meta{display:flex;align-items:center;gap:16px;flex-wrap:wrap}
.cc-product-dates{display:flex;align-items:center;gap:6px;font-size:12px;color:#999;margin-top:4px;flex-wrap:wrap}
.cc-product-dates .cc-sep{color:#ddd}
.cc-type-badge{font-size:11px;padding:3px 10px;border-radius:12px;font-weight:600;text-transform:uppercase;letter-spacing:.3px}
.cc-type-physical{background:#e8f5e9;color:#2e7d32}
.cc-type-script{background:#e3f2fd;color:#1565c0}
.cc-type-ebook{background:#fce4ec;color:#c62828}
.cc-rating-large{display:flex;align-items:center;gap:6px}
.cc-stars{display:inline-flex;gap:1px}
.star-filled{fill:#ffb400;stroke:#ffb400}
.star-empty{fill:transparent;stroke:#d0d5dd}
.cc-rating-value{font-size:16px;font-weight:700;color:#1a1a2e}
.cc-rating-count{font-size:14px;color:#888}
.cc-category-badge{background:#eef0f4;color:#555;padding:4px 12px;border-radius:20px;font-size:13px}
.cc-preview-wrap{background:#f7f8fa;border-radius:12px;overflow:hidden;margin-bottom:30px}
.cc-main-preview{text-align:center;padding:20px}
.cc-main-preview img{max-width:100%;max-height:500px;object-fit:contain}
.cc-thumb-strip{display:flex;gap:8px;padding:12px 16px;border-top:1px solid #eef0f4;overflow-x:auto}
.cc-thumb-item{width:70px;height:50px;border-radius:6px;overflow:hidden;cursor:pointer;border:2px solid transparent;flex-shrink:0;transition:border-color .15s}
.cc-thumb-item.active,.cc-thumb-item:hover{border-color:#2b4dff}
.cc-thumb-item img{width:100%;height:100%;object-fit:cover}
.cc-version-section{background:#fff;border:1px solid #eef0f4;border-radius:12px;overflow:hidden;margin-bottom:20px}
.cc-version-header{display:flex;align-items:center;gap:12px;padding:14px 20px;background:#f8faff;border-bottom:1px solid #eef0f4;flex-wrap:wrap}
.cc-version-badge-lg{font-size:14px;font-weight:700;color:#1565c0;background:#e3f2fd;padding:4px 12px;border-radius:6px}
.cc-version-label{font-size:13px;font-weight:600;color:#333}
.cc-version-meta{font-size:12px;color:#888;margin-left:auto}
.cc-version-date{font-size:12px;color:#888}
.cc-version-changelog{padding:16px 20px}
.cc-version-changelog strong{display:block;font-size:14px;color:#1a1a2e;margin-bottom:8px}
.cc-version-changelog-body{font-size:14px;color:#555;line-height:1.7;white-space:pre-wrap}
.cc-tabs-wrap{background:#fff;border-radius:12px;border:1px solid #eef0f4;overflow:hidden;margin-bottom:30px}
.cc-tabs{display:flex;border-bottom:1px solid #eef0f4;padding:0;background:#fafbfc}
.cc-tabs .nav-link{border:none;padding:14px 24px;font-size:14px;font-weight:600;color:#666;background:none;border-radius:0}
.cc-tabs .nav-link.active{color:#2b4dff;background:none;border-bottom:2px solid #2b4dff}
.tab-content{padding:24px}
.entry-content{font-size:15px;line-height:1.7;color:#444}
.cc-changelog-list{display:flex;flex-direction:column;gap:16px}
.cc-changelog-item{border:1px solid #eef0f4;border-radius:8px;overflow:hidden}
.cc-changelog-version{display:flex;align-items:center;justify-content:space-between;padding:10px 16px;background:#f7f8fa;border-bottom:1px solid #eef0f4}
.cc-version-badge{font-size:13px;font-weight:700;color:#1565c0;background:#e3f2fd;padding:3px 10px;border-radius:4px}
.cc-changelog-date{font-size:12px;color:#888}
.cc-changelog-body{padding:12px 16px;font-size:14px;color:#555;line-height:1.6}
.cc-review-item{display:flex;gap:14px;padding:16px 0;border-bottom:1px solid #f0f2f5}
.cc-review-avatar{width:44px;height:44px;border-radius:50%;overflow:hidden;flex-shrink:0}
.cc-review-avatar img{width:100%;height:100%;object-fit:cover}
.cc-review-body{flex:1}
.cc-review-name{font-weight:600;font-size:14px;color:#1a1a2e;margin-bottom:4px}
.cc-review-stars{font-size:13px;margin-bottom:4px}
.cc-review-body p{margin:4px 0;font-size:14px;color:#555}
.cc-review-date{font-size:12px;color:#999}
.cc-sidebar-card{background:#fff;border:1px solid #eef0f4;border-radius:12px;overflow:hidden;position:sticky;top:100px}
.cc-price-box{padding:24px;text-align:center;border-bottom:1px solid #f0f2f5}
.cc-price-amount{font-size:28px;font-weight:800;color:#1a1a2e}
.cc-sidebar-actions{padding:16px 20px;display:flex;flex-direction:column;gap:10px}
.cc-btn-primary{display:flex;align-items:center;justify-content:center;gap:8px;padding:12px;background:#2b4dff;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;transition:background .15s;text-decoration:none}
.cc-btn-primary:hover{background:#1a3dcc;color:#fff}
.cc-btn-secondary{display:flex;align-items:center;justify-content:center;gap:8px;padding:11px;background:#fff;color:#555;border:1px solid #dde0e4;border-radius:8px;font-size:14px;cursor:pointer;transition:all .15s;text-decoration:none}
.cc-btn-secondary:hover{border-color:#2b4dff;color:#2b4dff}
.cc-btn-wishlist{display:flex;align-items:center;justify-content:center;gap:8px;padding:11px;background:#fff;color:#888;border:1px solid #dde0e4;border-radius:8px;font-size:14px;cursor:pointer;transition:all .15s;text-decoration:none}
.cc-btn-wishlist:hover{color:#e74c3c;border-color:#e74c3c}
.cc-btn-wishlist.active{color:#e74c3c;border-color:#e74c3c;background:#fff5f5}
.cc-sidebar-info{padding:16px 20px;border-top:1px solid #f0f2f5}
.cc-info-row{display:flex;justify-content:space-between;padding:6px 0;font-size:13px}
.cc-info-label{color:#888}
.cc-info-value{color:#333;text-align:right}
.cc-info-value a{color:#2b4dff;text-decoration:none}
.cc-section-title{font-size:22px;font-weight:700;color:#1a1a2e;margin-bottom:24px}
@media(max-width:991px){.cc-sidebar-card{position:static}}
</style>

<script>
function swapPreview(el, src) {
    document.getElementById('mainPreview').src = src;
    document.querySelectorAll('.cc-thumb-item').forEach(i => i.classList.remove('active'));
    el.classList.add('active');
}
</script>
@endsection