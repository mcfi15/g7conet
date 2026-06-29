<div class="codecanyon-item" id="wishlist-item-{{ $product->id }}">
    <div class="cc-item-thumb">
        <a href="{{ route('product.view', $product->slug) }}">
            <img src="{{ asset($product->thumbnail_image) }}" alt="{{ $product->translate?->name }}">
        </a>
        @if($product->is_digital && $product->demo_url)
            <a href="{{ $product->demo_url }}" target="_blank" rel="noopener" class="cc-live-preview">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                {{ __('translate.Live Preview') }}
            </a>
        @endif
    </div>
    <div class="cc-item-body">
        <h5 class="cc-item-title">
            <a href="{{ route('product.view', $product->slug) }}">{{ $product->translate?->name ?? $product->name }}</a>
        </h5>
        <div class="cc-item-type">
            <span class="cc-mini-type cc-type-{{ $product->product_type }}">
                {{ $product->product_type === 'script' ? __('translate.Script') : ($product->product_type === 'ebook' ? __('translate.eBook') : __('translate.Physical')) }}
            </span>
        </div>
        <div class="cc-item-meta">
            @php
                $avgRating = $product->reviews->avg('rating') ?? 0;
                $reviewCount = $product->reviews->count() ?? 0;
            @endphp
            <div class="cc-rating">
                <div class="cc-stars">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="{{ $i <= round($avgRating) ? 'star-filled' : 'star-empty' }}" width="14" height="14" viewBox="0 0 24 24">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    @endfor
                </div>
                <span class="cc-rating-count">({{ $reviewCount }})</span>
            </div>
        </div>
        <div class="cc-item-footer">
            <span class="cc-price">{!! $product->price_display !!}</span>
            <div class="cc-actions">
                <button class="cc-cart-btn cart-add-btn" data-product-id="{{ $product->id }}" title="{{ __('translate.Add to Cart') }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                </button>
                <a href="javascript:void(0)"
                   class="cc-wishlist {{ auth()->check() && in_array($product->id, auth()->user()->wishlists->pluck('product_id')->toArray()) ? 'active' : '' }}"
                   data-url="{{ route('user.wishlist.store') }}"
                   onclick="addToWishlist({{ $product->id }}, this)"
                   title="{{ __('translate.Wishlist') }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </a>
            </div>
        </div>
    </div>
</div>
