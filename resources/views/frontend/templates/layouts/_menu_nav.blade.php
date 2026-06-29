<div class="menu-block-wrapper">
    <div class="menu-overlay"></div>
    <nav class="menu-block" id="append-menu-header">
        <div class="mobile-menu-head">
            <div class="go-back">
                <i class="fa fa-angle-left"></i>
            </div>
            <div class="current-menu-title"></div>
            <div class="mobile-menu-close">&times;</div>
        </div>

        <div class="mobile-utility-bar">
            <div class="mobile-utility-item mobile-currency-wrap">
                <span class="mobile-utility-icon"><i class="ri-money-dollar-circle-line"></i></span>
                <form action="{{ route('currency-switcher') }}" class="mobile-utility-form">
                    <select name="currency_code" class="mobile-utility-select" onchange="this.form.submit()">
                        @foreach ($currency_list as $currency_item)
                            <option {{ Session::get('currency_code') == $currency_item->currency_code ? 'selected' : '' }} value="{{ $currency_item->currency_code }}">{{ $currency_item->currency_name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="mobile-utility-item mobile-lang-wrap">
                <span class="mobile-utility-icon"><i class="ri-global-line"></i></span>
                <form action="{{ route('language-switcher') }}" class="mobile-utility-form">
                    <select name="lang_code" class="mobile-utility-select" onchange="this.form.submit()">
                        @foreach ($language_list as $language_item)
                            <option {{ Session::get('front_lang') == $language_item->lang_code ? 'selected' : '' }} value="{{ $language_item->lang_code }}">{{ $language_item->lang_name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="mobile-utility-item mobile-utility-search">
                <i class="ri-search-line"></i>
            </div>
        </div>

        <div class="mobile-search-bar">
            <form action="{{ route('product.search') }}" method="GET">
                <input type="search" name="query" placeholder="{{ __('translate.Search here...') }}" required>
                <button type="submit"><i class="ri-search-line"></i></button>
            </form>
        </div>

        <div class="mobile-user-section">
            @auth
                <a href="{{ route('user.dashboard') }}" class="mobile-user-link">
                    <i class="ri-dashboard-line"></i>
                    <span>{{ __('translate.Dashboard') }}</span>
                </a>
                <a href="{{ route('user.logout') }}" class="mobile-user-link" onclick="event.preventDefault(); document.getElementById('mobile-logout-form').submit();">
                    <i class="ri-logout-box-r-line"></i>
                    <span>{{ __('translate.Logout') }}</span>
                </a>
                <form id="mobile-logout-form" action="{{ route('user.logout') }}" method="GET" style="display:none;">@csrf</form>
            @else
                <a href="{{ route('user.login') }}" class="mobile-user-link">
                    <i class="ri-user-line"></i>
                    <span>{{ __('translate.Login') }}</span>
                </a>
                <a href="{{ route('user.register') }}" class="mobile-user-link">
                    <i class="ri-user-add-line"></i>
                    <span>{{ __('translate.Register') }}</span>
                </a>
            @endauth
        </div>

        <ul class="site-menu-main {{ $menuTheme ?? '' }}">

            @php
                $currentTheme = Modules\GlobalSetting\App\Models\GlobalSetting::where('key', 'selected_theme')->first()?->value ?? 'all_theme';
            @endphp

            @if(config('app.env') === 'DEMO' || $currentTheme === 'all_theme')
                <li class="nav-item nav-item-has-children">
                    <a href="#" class="nav-link-item drop-trigger">{{ __('translate.Home') }} <i
                            class="ri-arrow-down-s-fill"></i></a>
                    <ul class="sub-menu" id="submenu-1">
                        <li class="sub-menu--item">
                            <a href="{{ route('home', ['theme' => 'main_demo']) }}">
                                <span class="menu-item-text {{ $currentTheme === 'main_demo' ? 'active' : '' }}">{{ __('translate.Main Demo') }}</span>
                            </a>
                        </li>
                        <li class="sub-menu--item">
                            <a href="{{ route('home', ['theme' => 'it_solutions']) }}">
                                <span class="menu-item-text {{ $currentTheme === 'it_solutions' ? 'active' : '' }}">{{ __('translate.IT Solutions') }}</span>
                            </a>
                        </li>
                        <li class="sub-menu--item">
                            <a href="{{ route('home', ['theme' => 'tech_agency']) }}">
                                <span class="menu-item-text {{ $currentTheme === 'tech_agency' ? 'active' : '' }}">{{ __('translate.Tech Agency')}}</span>
                            </a>
                        </li>
                        <li class="sub-menu--item">
                            <a href="{{ route('home', ['theme' => 'startup_home']) }}">
                                <span class="menu-item-text {{ $currentTheme === 'startup_home' ? 'active' : '' }}">{{ __('translate.Startup Home') }}</span>
                            </a>
                        </li>
                        <li class="sub-menu--item">
                            <a href="{{ route('home', ['theme' => 'it_consulting']) }}">
                                <span class="menu-item-text {{ $currentTheme === 'it_consulting' ? 'active' : '' }}">{{ __('translate.IT Consulting') }}</span>
                            </a>
                        </li>
                        <li class="sub-menu--item">
                            <a href="{{ route('home', ['theme' => 'soft_company']) }}">
                                <span class="menu-item-text {{ $currentTheme === 'soft_company' ? 'active' : '' }}">{{ __('translate.Software Company') }}</span>
                            </a>
                        </li>
                        <li class="sub-menu--item">
                            <a href="{{ route('home', ['theme' => 'digital_agency']) }}">
                                <span class="menu-item-text {{ $currentTheme === 'digital_agency' ? 'active' : '' }}">{{ __('translate.Digital Agency') }}</span>
                            </a>
                        </li>
                        <li class="sub-menu--item">
                            <a href="{{ route('home', ['theme' => 'tech_company']) }}">
                                <span class="menu-item-text {{ $currentTheme === 'tech_company' ? 'active' : '' }}">{{ __('translate.Tech Company') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @else
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link-item">
                        <span class="menu-item-text active">{{ __('translate.Home') }}</span>
                    </a>
                </li>
            @endif

            <li class="nav-item">
                <a href="{{ route('product.shop') }}" class="nav-link-item">
                    <span class="menu-item-text">{{ __('translate.Marketplace') }}</span>
                </a>
            </li>

            @if($nav_categories->isNotEmpty())
            <li class="nav-item nav-item-has-children">
                <a href="#" class="nav-link-item drop-trigger">{{ __('translate.Categories') }} <i class="ri-arrow-down-s-fill"></i></a>
                <ul class="sub-menu" id="submenu-categories">
                    @foreach($nav_categories as $nav_category)
                        @if($nav_category->subcategories->isNotEmpty())
                        <li class="sub-menu--item nav-item-has-children">
                            <a href="{{ route('product.shop', ['categories[]' => $nav_category->id]) }}" class="drop-trigger">
                                {{ $nav_category->front_translate->name ?? $nav_category->translate->name ?? '' }}<i class="ri-arrow-down-s-fill"></i>
                            </a>
                            <ul class="sub-menu shape-none" id="submenu-cat-{{ $nav_category->id }}">
                                @foreach($nav_category->subcategories as $sub)
                                <li class="sub-menu--item">
                                    <a href="{{ route('product.shop', ['categories[]' => $nav_category->id]) }}">
                                        {{ $sub->front_translate->name ?? $sub->translate->name ?? '' }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </li>
                        @else
                        <li class="sub-menu--item">
                            <a href="{{ route('product.shop', ['categories[]' => $nav_category->id]) }}">
                                {{ $nav_category->front_translate->name ?? $nav_category->translate->name ?? '' }}
                            </a>
                        </li>
                        @endif
                    @endforeach
                </ul>
            </li>
            @endif

            <li class="nav-item nav-item-has-children">
                <a href="#" class="nav-link-item drop-trigger">{{ __('translate.Business Tools') }} <i class="ri-arrow-down-s-fill"></i></a>
                <ul class="sub-menu" id="submenu-biztools">
                    @if (($general_setting->invoice_status ?? '1') == '1')
                    <li class="sub-menu--item">
                        <a href="{{ route('invoice.create') }}">{{ __('translate.Invoice Generator') }}</a>
                    </li>
                    @endif
                    @if (($general_setting->qr_code_status ?? '1') == '1')
                    <li class="sub-menu--item">
                        <a href="{{ route('qrcode.create') }}">{{ __('translate.QR Code Generator') }}</a>
                    </li>
                    @endif
                </ul>
            </li>

            @php
                $blogType = Modules\GlobalSetting\App\Models\GlobalSetting::where('key', 'blog_type')->first()?->value ?? 'default';
                $isGrid = $blogType === 'grid';
            @endphp

            <li class="nav-item">
                <a href="{{ route('blogs', ['type' => 'grid']) }}" class="nav-link-item">
                    <span class="menu-item-text">{{ __('translate.Blog') }}</span>
                </a>
            </li>

        </ul>

        <div class="mobile-get-in-touch">
            <a href="{{ route('contact-us') }}" class="mobile-cta-btn">{{ __('translate.Get in Touch') }}</a>
        </div>

        @php
            if (auth()->check()) {
                $mobileCartTotal = \Modules\Ecommerce\Entities\Cart::where('user_id', auth()->id())->count();
            } else {
                $mobileCartTotal = \Modules\Ecommerce\Entities\Cart::where('session_id', session()->get('session_id'))->count();
            }
        @endphp

        <div class="mobile-commerce-section">
            <a href="{{ route('cart.cart') }}" class="mobile-commerce-item">
                <i class="ri-shopping-cart-line"></i>
                <span class="mobile-commerce-label">{{ __('translate.Cart') }}</span>
                <span class="mobile-commerce-badge">{{ $mobileCartTotal }}</span>
            </a>
            <a href="{{ route('product.shop') }}" class="mobile-commerce-item">
                <i class="ri-store-2-line"></i>
                <span class="mobile-commerce-label">{{ __('translate.Shop') }}</span>
            </a>
            <a href="{{ route('contact-us') }}" class="mobile-commerce-item">
                <i class="ri-customer-service-2-line"></i>
                <span class="mobile-commerce-label">{{ __('translate.Contact') }}</span>
            </a>
        </div>
    </nav>
</div>