<!DOCTYPE html>
<html lang="en">
    @include('frontend.head')
  <body>

    <!-- Menu Start -->
    <div class="optech-preloader-wrap">
        <div class="optech-preloader">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <!-- End preloader -->


    <!-- progress circle -->
    <div class="paginacontainer">
        <div class="progress-wrap">
            <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
                <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
            </svg>
            <div class="top-arrow">
                <i class="ri-arrow-up-s-line"></i>
            </div>
        </div>
    </div>

    @yield('front-content')

    {{-- @if((($general_setting->invoice_status ?? '0') == '1' || ($general_setting->qr_code_status ?? '0') == '1') && !request()->routeIs('invoice.*') && !request()->routeIs('qrcode.*'))
    <div style="background:#1a1a2e;padding:12px 0;text-align:center;border-top:1px solid rgba(255,255,255,0.08)">
        <div class="container">
            <p style="margin:0;font-size:13px;color:rgba(255,255,255,0.6)">
                @if(($general_setting->invoice_status ?? '0') == '1')
                <strong style="color:#fff">{{ __('translate.Invoice Generator') }}</strong>
                <a href="{{ route('invoice.create') }}" style="color:var(--accent-color);text-decoration:underline;margin:0 4px">{{ __('translate.Create an invoice') }}</a>
                <span style="margin:0 4px;color:rgba(255,255,255,0.3)">|</span>
                @endif
                @if(($general_setting->qr_code_status ?? '0') == '1')
                <strong style="color:#fff">{{ __('translate.QR Code Generator') }}</strong>
                <a href="{{ route('qrcode.create') }}" style="color:var(--accent-color);text-decoration:underline;margin:0 4px">{{ __('translate.Generate QR Code') }}</a>
                <span style="margin:0 4px;color:rgba(255,255,255,0.3)">|</span>
                @endif
                {{ __('translate.Guest content is deleted after 24 hours. Registered users save for 3 months.') }}
                <a href="{{ route('user.register') }}" style="color:var(--accent-color);text-decoration:underline;margin-left:4px">{{ __('translate.Sign up free') }}</a>
            </p>
        </div>
    </div>
    @endif --}}

    @include('frontend.script')

    @stack('js_section')

  </body>
</html>
