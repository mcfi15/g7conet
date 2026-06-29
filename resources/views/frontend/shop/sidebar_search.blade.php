<div class="col-xl-3 col-lg-4 col-md-5">
    <div class="shop_sidebar">
        <form id="filterForm" action="{{ route('product.search') }}" method="GET">
            <div class="shop_sidebar_item mt-0 pt-0 border-0">
                <div class="shop_sidebar_text">
                    <h6>{{ __('translate.Search') }}</h6>
                </div>
                <div class="shop_sidebar_search_box">
                    <input type="text" name="query" placeholder="{{ __('translate.Search items...') }}" value="{{ request('query') }}">
                    <button type="submit" class="search_btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </button>
                </div>
            </div>

            @if(isset($brands) && $brands->count() > 0)
            <div class="shop_sidebar_item">
                <div class="shop_sidebar_text">
                    <h6>{{ __('translate.Brand') }}</h6>
                </div>
                <select class="form-select" name="brand" onchange="this.form.submit()">
                    <option value="">{{ __('translate.All Brands') }}</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                            {{ $brand->translate?->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="shop_sidebar_item">
                <div class="shop_sidebar_text">
                    <h6>{{ __('translate.Category') }}</h6>
                </div>
                <div class="shop_sidebar_item_box_main fst">
                    @foreach($categories as $category)
                        <div class="shop_sidebar_item_box">
                            <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $category->id }}" id="category_{{ $category->id }}" {{ in_array($category->id, (array)request('categories', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="category_{{ $category->id }}">
                                {{ $category->translate->name }} ({{ $category->products_count }})
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="shop_sidebar_item">
                <div class="shop_sidebar_text">
                    <h6>{{ __('translate.Price Range') }}</h6>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <input type="number" class="form-control form-control-sm" name="min_price" placeholder="{{ __('translate.Min') }}" value="{{ request('min_price') }}">
                    </div>
                    <div class="col-6">
                        <input type="number" class="form-control form-control-sm" name="max_price" placeholder="{{ __('translate.Max') }}" value="{{ request('max_price') }}">
                    </div>
                </div>
            </div>

            @if(request()->anyFilled(['query', 'brand', 'categories', 'min_price', 'max_price']))
            <div class="shop_sidebar_item">
                <a href="{{ route('product.shop') }}" class="cc-clear-btn">{{ __('translate.Clear Filters') }}</a>
            </div>
            @endif

            <button type="submit" class="optech-default-btn w-100" data-text="{{ __('translate.Apply Now') }}">
                <span class="btn-wraper">{{ __('translate.Apply Now') }}</span>
            </button>
        </form>
    </div>
</div>

<style>
.shop_sidebar_item{margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid #eef0f4}
.shop_sidebar_text h6{font-size:14px;font-weight:700;color:#1a1a2e;margin-bottom:12px;text-transform:uppercase;letter-spacing:.3px}
.shop_sidebar_search_box{position:relative}
.shop_sidebar_search_box input{width:100%;padding:10px 40px 10px 14px;border:1px solid #dde0e4;border-radius:6px;font-size:13px;outline:none;transition:border-color .2s}
.shop_sidebar_search_box input:focus{border-color:#2b4dff}
.shop_sidebar_search_box .search_btn{position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;color:#888;cursor:pointer;padding:4px}
.cc-clear-btn{display:block;text-align:center;padding:6px;font-size:13px;color:#888;text-decoration:none;border:1px dashed #dde0e4;border-radius:6px;transition:all .15s}
.cc-clear-btn:hover{color:#e74c3c;border-color:#e74c3c}
</style>
