@extends('master_layout')
@section('title')
    <title>{{ $pageTitle ?? '' }} - {{ config('app.name') }}</title>
@endsection
@section('new-layout')
<div class="optech-breadcrumb" style="background-image: url({{ asset($general_setting->breadcrumb_image) }})">
    <div class="container">
        <h1 class="post__title">{{ __('translate.QR Code Generator') }}</h1>
        <nav class="breadcrumbs">
            <ul>
                <li><a href="{{ route('home') }}">{{ __('translate.Home') }}</a></li>
                <li aria-current="page">{{ __('translate.QR Code Generator') }}</li>
            </ul>
        </nav>
    </div>
</div> 

@if (!auth('web')->check())
    @if ($limitReached)
    <div class="section" style="padding-top:40px;padding-bottom:0">
        <div class="container">
            <div style="background:linear-gradient(135deg,#fffbeb,#fff3cd);border:1px solid #ffc107;border-radius:12px;padding:28px 30px;margin-bottom:28px;text-align:center">
                <i class="ri-error-warning-line" style="font-size:36px;color:#e67e22;display:block;margin-bottom:10px"></i>
                <h4 style="font-size:18px;font-weight:700;color:var(--heading-color);margin:0 0 8px">{{ __('translate.Daily Limit Reached') }}</h4>
                <p style="font-size:14px;color:var(--body-color);margin:0 0 16px">{{ __('translate.You have reached the daily limit of') }} {{ $dailyLimit }} {{ __('translate.QR codes.') }} {{ __('translate.Sign up for a free account to continue creating unlimited QR codes.') }}</p>
                <a href="{{ route('user.register') }}" class="qr-btn qr-btn--primary" style="padding:12px 28px">{{ __('translate.Create Free Account') }}</a>
                <a href="{{ route('user.login') }}" style="display:inline-flex;text-decoration:none;padding:12px 28px;margin-left:8px;color:var(--accent-color);font-weight:600">{{ __('translate.Sign In') }}</a>
            </div>
        </div>
    </div>
    @else
    <div class="section" style="padding-top:40px;padding-bottom:0">
        <div class="container">
            <div style="background:linear-gradient(135deg,#e8f5e9,#f1f8e9);border:1px solid #66bb6a;border-radius:12px;padding:14px 24px;margin-bottom:28px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
                <span style="font-size:14px;color:var(--body-color)"><i class="ri-information-line" style="color:#43a047;margin-right:6px"></i> {{ __('translate.Guest limit') }}: <strong>{{ $dailyLimit }}</strong> {{ __('translate.QR codes per day') }}. {{ __('translate.Sign up to save QR codes for 3 months and get unlimited access.') }}</span>
                <a href="{{ route('user.register') }}" style="text-decoration:none;white-space:nowrap;padding:8px 20px;color:var(--accent-color);font-weight:600;border:1px solid var(--accent-color);border-radius:8px"><i class="ri-user-add-line"></i> {{ __('translate.Sign Up Free') }}</a>
            </div>
        </div>
    </div>
    @endif
@else
<div class="section" style="padding-top:40px;padding-bottom:0">
    <div class="container">
        <div style="background:linear-gradient(135deg,#e3f2fd,#e8f5e9);border:1px solid #42a5f5;border-radius:12px;padding:14px 24px;margin-bottom:28px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            <i class="ri-vip-crown-line" style="color:#1565c0;font-size:20px"></i>
            <span style="font-size:14px;color:var(--body-color)">{{ __('translate.You are signed in as') }} <strong>{{ auth('web')->user()->name }}</strong>. {{ __('translate.Your QR codes are saved for 3 months.') }} <a href="{{ route('user.qr-codes') }}" style="color:var(--accent-color);font-weight:600;text-decoration:none">{{ __('translate.View My QR Codes') }}</a></span>
        </div>
    </div>
</div>
@endif
<div class="section optech-section-padding2">
    <div class="container">
        <div class="qr-generator-layout">
            <div class="qr-control-panel">

                <div class="qr-card">
                    <h4 class="qr-card-title">{{ __('translate.Content Type') }}</h4>
                    <div class="qr-content-type-grid">
                        <button class="qr-type-btn active" data-type="url" title="{{ __('translate.Link / URL') }}"><i class="ri-link"></i><span>{{ __('translate.URL') }}</span></button>
                        <button class="qr-type-btn" data-type="text" title="{{ __('translate.Text') }}"><i class="ri-text"></i><span>{{ __('translate.Text') }}</span></button>
                        <button class="qr-type-btn" data-type="email" title="{{ __('translate.Email') }}"><i class="ri-mail-line"></i><span>{{ __('translate.Email') }}</span></button>
                        <button class="qr-type-btn" data-type="call" title="{{ __('translate.Call') }}"><i class="ri-phone-line"></i><span>{{ __('translate.Call') }}</span></button>
                        <button class="qr-type-btn" data-type="sms" title="{{ __('translate.SMS') }}"><i class="ri-chat-1-line"></i><span>{{ __('translate.SMS') }}</span></button>
                        <button class="qr-type-btn" data-type="whatsapp" title="{{ __('translate.WhatsApp') }}"><i class="ri-whatsapp-line"></i><span>{{ __('translate.WhatsApp') }}</span></button>
                        <button class="qr-type-btn" data-type="wifi" title="{{ __('translate.Wi-Fi') }}"><i class="ri-wifi-line"></i><span>{{ __('translate.Wi-Fi') }}</span></button>
                        <button class="qr-type-btn" data-type="vcard" title="{{ __('translate.V-Card') }}"><i class="ri-user-smile-line"></i><span>{{ __('translate.V-Card') }}</span></button>
                        <button class="qr-type-btn" data-type="event" title="{{ __('translate.Event') }}"><i class="ri-calendar-event-line"></i><span>{{ __('translate.Event') }}</span></button>
                    </div>
                </div>

                <div class="qr-card" id="contentInputCard">
                    <h4 class="qr-card-title">{{ __('translate.Content') }}</h4>

                    <div class="qr-content-fields active" data-for="url">
                        <div class="qr-field"><label>{{ __('translate.Website URL') }}</label><input type="text" id="fieldUrl" placeholder="https://example.com" value="https://"></div>
                    </div>

                    <div class="qr-content-fields" data-for="text">
                        <div class="qr-field"><label>{{ __('translate.Text') }}</label><textarea id="fieldText" rows="3" placeholder="{{ __('translate.Enter your text here') }}"></textarea></div>
                    </div>

                    <div class="qr-content-fields" data-for="email">
                        <div class="qr-field"><label>{{ __('translate.Recipient Email') }}</label><input type="email" id="fieldEmail" placeholder="user@example.com"></div>
                        <div class="qr-field"><label>{{ __('translate.Subject') }}</label><input type="text" id="fieldEmailSubject" placeholder="{{ __('translate.Email subject') }}"></div>
                        <div class="qr-field"><label>{{ __('translate.Body') }}</label><textarea id="fieldEmailBody" rows="2" placeholder="{{ __('translate.Email body text') }}"></textarea></div>
                    </div>

                    <div class="qr-content-fields" data-for="call">
                        <div class="qr-field"><label>{{ __('translate.Phone Number') }}</label><input type="tel" id="fieldCall" placeholder="+1234567890"></div>
                    </div>

                    <div class="qr-content-fields" data-for="sms">
                        <div class="qr-field"><label>{{ __('translate.Phone Number') }}</label><input type="tel" id="fieldSmsPhone" placeholder="+1234567890"></div>
                        <div class="qr-field"><label>{{ __('translate.Message') }}</label><textarea id="fieldSmsMsg" rows="2" placeholder="{{ __('translate.Your message') }}"></textarea></div>
                    </div>

                    <div class="qr-content-fields" data-for="whatsapp">
                        <div class="qr-field"><label>{{ __('translate.WhatsApp Number') }}</label><input type="tel" id="fieldWaPhone" placeholder="1234567890 (without +)"></div>
                        <div class="qr-field"><label>{{ __('translate.Message') }}</label><textarea id="fieldWaMsg" rows="2" placeholder="{{ __('translate.Pre-filled message') }}"></textarea></div>
                    </div>

                    <div class="qr-content-fields" data-for="wifi">
                        <div class="qr-field"><label>{{ __('translate.Network Name (SSID)') }}</label><input type="text" id="fieldWifiSsid" placeholder="MyWiFi"></div>
                        <div class="qr-field"><label>{{ __('translate.Password') }}</label><input type="text" id="fieldWifiPass" placeholder="password"></div>
                        <div class="qr-field"><label>{{ __('translate.Network Type') }}</label><select id="fieldWifiType"><option value="WPA">WPA/WPA2</option><option value="WEP">WEP</option><option value="nopass">{{ __('translate.Unsecured') }}</option></select></div>
                    </div>

                    <div class="qr-content-fields" data-for="vcard">
                        <div class="qr-field"><label>{{ __('translate.Full Name') }}</label><input type="text" id="fieldVcName" placeholder="John Doe"></div>
                        <div class="qr-field"><label>{{ __('translate.Organization') }}</label><input type="text" id="fieldVcOrg" placeholder="Company Inc."></div>
                        <div class="qr-field"><label>{{ __('translate.Title') }}</label><input type="text" id="fieldVcTitle" placeholder="Software Engineer"></div>
                        <div class="qr-field"><label>{{ __('translate.Phone') }}</label><input type="tel" id="fieldVcPhone" placeholder="+1234567890"></div>
                        <div class="qr-field"><label>{{ __('translate.Email') }}</label><input type="email" id="fieldVcEmail" placeholder="john@example.com"></div>
                        <div class="qr-field"><label>{{ __('translate.Website') }}</label><input type="url" id="fieldVcUrl" placeholder="https://example.com"></div>
                    </div>

                    <div class="qr-content-fields" data-for="event">
                        <div class="qr-field"><label>{{ __('translate.Event Title') }}</label><input type="text" id="fieldEvTitle" placeholder="Meeting"></div>
                        <div class="qr-field"><label>{{ __('translate.Start Date & Time') }}</label><input type="datetime-local" id="fieldEvStart"></div>
                        <div class="qr-field"><label>{{ __('translate.End Date & Time') }}</label><input type="datetime-local" id="fieldEvEnd"></div>
                        <div class="qr-field"><label>{{ __('translate.Location') }}</label><input type="text" id="fieldEvLocation" placeholder="Room 1 or Online"></div>
                    </div>
                </div>
                <div class="qr-card">
                    <div class="qr-accordion">
                        <button class="qr-accordion-trigger active" type="button" data-target="designBody">{{ __('translate.Design & Customization') }} <i class="ri-arrow-down-s-line"></i></button>
                        <div class="qr-accordion-body" id="designBody" style="display:block">

                            <div class="qr-section-group">
                                <h5 class="qr-section-label">{{ __('translate.Colors') }}</h5>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="qr-field"><label>{{ __('translate.Foreground') }}</label><div class="qr-color-input"><input type="color" id="fgColor" value="#000000"><input type="text" id="fgColorText" value="#000000" maxlength="7"></div></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="qr-field"><label>{{ __('translate.Background') }}</label><div class="qr-color-input"><input type="color" id="bgColor" value="#FFFFFF"><input type="text" id="bgColorText" value="#FFFFFF" maxlength="7"></div></div>
                                    </div>
                                </div>
                                <div class="qr-field" style="margin-top:8px">
                                    <label><input type="checkbox" id="gradientToggle"> {{ __('translate.Use gradient foreground') }}</label>
                                </div>
                                <div id="gradientOptions" style="display:none;margin-top:8px">
                                    <div class="row">
                                        <div class="col-sm-6"><div class="qr-field"><label>{{ __('translate.Gradient Start') }}</label><div class="qr-color-input"><input type="color" id="gradStart" value="#FF0000"><input type="text" id="gradStartText" value="#FF0000" maxlength="7"></div></div></div>
                                        <div class="col-sm-6"><div class="qr-field"><label>{{ __('translate.Gradient End') }}</label><div class="qr-color-input"><input type="color" id="gradEnd" value="#0000FF"><input type="text" id="gradEndText" value="#0000FF" maxlength="7"></div></div></div>
                                    </div>
                                </div>
                            </div>

                            <div class="qr-section-group">
                                <h5 class="qr-section-label">{{ __('translate.Eye Colors') }}</h5>
                                <div class="row">
                                    <div class="col-sm-6"><div class="qr-field"><label>{{ __('translate.Eye Border') }}</label><div class="qr-color-input"><input type="color" id="eyeBorderColor" value="#000000"><input type="text" id="eyeBorderColorText" value="#000000" maxlength="7"></div></div></div>
                                    <div class="col-sm-6"><div class="qr-field"><label>{{ __('translate.Eye Center') }}</label><div class="qr-color-input"><input type="color" id="eyeCenterColor" value="#000000"><input type="text" id="eyeCenterColorText" value="#000000" maxlength="7"></div></div></div>
                                </div>
                            </div>

                            <div class="qr-section-group">
                                <h5 class="qr-section-label">{{ __('translate.Dot Pattern') }}</h5>
                                <div class="qr-field">
                                    <div class="qr-style-options" id="dotStyleOptions">
                                        <button class="qr-style-btn active" data-value="square"><span class="qr-sp qr-sp-square"></span>{{ __('translate.Square') }}</button>
                                        <button class="qr-style-btn" data-value="rounded"><span class="qr-sp qr-sp-rounded"></span>{{ __('translate.Rounded') }}</button>
                                        <button class="qr-style-btn" data-value="dots"><span class="qr-sp qr-sp-dots"></span>{{ __('translate.Dots') }}</button>
                                        <button class="qr-style-btn" data-value="classy"><span class="qr-sp qr-sp-classy"></span>{{ __('translate.Classy') }}</button>
                                        <button class="qr-style-btn" data-value="extra-rounded"><span class="qr-sp qr-sp-erounded"></span>{{ __('translate.X-Rounded') }}</button>
                                    </div>
                                </div>
                            </div>

                            <div class="qr-section-group">
                                <h5 class="qr-section-label">{{ __('translate.Eye Shape') }}</h5>
                                <div class="qr-field">
                                    <div class="qr-style-options" id="eyeStyleOptions">
                                        <button class="qr-style-btn active" data-value="square"><span class="qr-sp qr-sp-square"></span>{{ __('translate.Square') }}</button>
                                        <button class="qr-style-btn" data-value="rounded"><span class="qr-sp qr-sp-rounded"></span>{{ __('translate.Rounded') }}</button>
                                        <button class="qr-style-btn" data-value="circle"><span class="qr-sp qr-sp-dots"></span>{{ __('translate.Circular') }}</button>
                                    </div>
                                </div>
                            </div>

                            <div class="qr-section-group">
                                <h5 class="qr-section-label">{{ __('translate.Logo / Branding') }}</h5>
                                <div class="qr-field">
                                    <label class="qr-file-label"><i class="ri-upload-2-line"></i> {{ __('translate.Upload Logo Image') }}<input type="file" id="logoUpload" accept="image/*"></label>
                                    <button type="button" class="qr-btn-sm" id="removeLogoBtn" style="display:none;margin-top:8px"><i class="ri-close-line"></i> {{ __('translate.Remove') }}</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="qr-card">
                    <div class="qr-accordion">
                        <button class="qr-accordion-trigger" type="button" data-target="frameBody">{{ __('translate.Frame & Label') }} <i class="ri-arrow-down-s-line"></i></button>
                        <div class="qr-accordion-body" id="frameBody">
                            <div class="qr-field"><label><input type="checkbox" id="showFrame" checked> {{ __('translate.Show frame') }}</label></div>
                            <div class="qr-field">
                                <label>{{ __('translate.Frame style') }}</label>
                                <div class="qr-style-options" id="frameStyleOptions">
                                    <button class="qr-style-btn active" data-value="panel">{{ __('translate.Panel') }}</button>
                                    <button class="qr-style-btn" data-value="outline">{{ __('translate.Outline') }}</button>
                                    <button class="qr-style-btn" data-value="shadow">{{ __('translate.Shadow') }}</button>
                                    <button class="qr-style-btn" data-value="bar">{{ __('translate.Bottom Bar') }}</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6"><div class="qr-field"><label>{{ __('translate.Frame text') }}</label><input type="text" id="frameText" value="{{ __('translate.SCAN ME') }}" maxlength="20"></div></div>
                                <div class="col-sm-6"><div class="qr-field"><label>{{ __('translate.Font size') }}</label><select id="fontSize"><option value="12">12px</option><option value="14" selected>14px</option><option value="16">16px</option><option value="18">18px</option><option value="20">20px</option></select></div></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4"><div class="qr-field"><label>{{ __('translate.Frame color') }}</label><div class="qr-color-input"><input type="color" id="frameColor" value="#000000"><input type="text" id="frameColorText" value="#000000" maxlength="7"></div></div></div>
                                <div class="col-sm-4"><div class="qr-field"><label>{{ __('translate.Text color') }}</label><div class="qr-color-input"><input type="color" id="textColor" value="#FFFFFF"><input type="text" id="textColorText" value="#FFFFFF" maxlength="7"></div></div></div>
                                <div class="col-sm-4"><div class="qr-field"><label>{{ __('translate.Margin') }}</label><select id="qrMargin"><option value="0">0</option><option value="1">1</option><option value="2" selected>2</option><option value="3">3</option><option value="4">4</option></select></div></div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(!$limitReached)
                <div class="qr-card">
                    <form id="saveForm" action="{{ route('qrcode.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="content" id="saveContent">
                        <input type="hidden" name="foreground_color" id="saveFg">
                        <input type="hidden" name="background_color" id="saveBg">
                        <input type="hidden" name="size" id="saveSize">
                        <input type="hidden" name="format" id="saveFormat" value="png">
                        <input type="hidden" name="dot_style" id="saveDotStyle">
                        <input type="hidden" name="eye_style" id="saveEyeStyle">
                        <input type="hidden" name="eye_border_color" id="saveEyeBorder">
                        <input type="hidden" name="eye_center_color" id="saveEyeCenter">
                        <input type="hidden" name="gradient_enabled" id="saveGradientEnabled">
                        <input type="hidden" name="gradient_start" id="saveGradientStart">
                        <input type="hidden" name="gradient_end" id="saveGradientEnd">
                        <input type="hidden" name="frame_style" id="saveFrameStyle">
                        <input type="hidden" name="frame_text" id="saveFrameText">
                        <input type="hidden" name="frame_color" id="saveFrameColor">
                        <input type="hidden" name="frame_text_color" id="saveFrameTextColor">
                        <input type="hidden" name="frame_margin" id="saveFrameMargin">
                        <input type="hidden" name="frame_font_size" id="saveFrameFontSize">
                        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
                            @if($general_setting->recaptcha_status == 1)
                            <div class="g-recaptcha" data-sitekey="{{ $general_setting->recaptcha_site_key }}" style="flex-shrink:0"></div>
                            @endif
                            <button type="submit" class="qr-btn qr-btn--primary"><i class="ri-save-line"></i> {{ __('translate.Save QR Code') }}</button>
                            <span style="font-size:13px;color:var(--body-color)"><i class="ri-information-line" style="margin-right:4px"></i> {{ auth('web')->check() ? __('translate.Saved to your account for 3 months.') : __('translate.Guests: saved for 24 hours.') }}</span>
                        </div>
                    </form>
                </div>
                @endif

            </div>
            <div class="qr-preview-panel">
                <div class="qr-preview-sticky">
                    <div class="qr-card" style="text-align:center">
                        <h4 class="qr-card-title" style="text-align:left">{{ __('translate.Preview') }}</h4>
                        <div class="qr-preview-canvas-wrap">
                            <div class="qr-frame-wrapper" id="qrFrameWrapper">
                                <div id="qrCanvasContainer"></div>
                                <div id="qrFrameLabel" class="qr-frame-label" style="display:none">{{ __('translate.SCAN ME') }}</div>
                            </div>
                        </div>
                        <p style="font-size:13px;color:var(--body-color);word-break:break-all;max-width:400px;margin:12px auto 0" id="previewContentText">—</p>
                    </div>

                    <div class="qr-card">
                        <button class="qr-btn qr-btn--primary" id="downloadPngBtn" style="width:100%;justify-content:center;padding:14px"><i class="ri-download-2-line"></i> {{ __('translate.Download PNG') }}</button>
                    </div>

                    @if(!auth('web')->check() && !$limitReached)
                    <div class="qr-card" style="background:linear-gradient(135deg,#e8eaf6,#e3f2fd);border:1px solid #7986cb;text-align:center">
                        <p style="margin:0;font-size:14px;color:var(--body-color)"><strong>{{ __('translate.Sign up free') }}</strong> {{ __('translate.to save your QR codes for 3 months and get unlimited access.') }} <a href="{{ route('user.register') }}" style="color:var(--accent-color);font-weight:700;text-decoration:underline;display:block;margin-top:6px">{{ __('translate.Create Free Account') }} →</a></p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('assets/js/qrcode-styling.min.js') }}"></script>
<script>
(function() {
    'use strict';

    var currentContentType = 'url';
    var logoDataURL = null;
    var qrInstance = null;
    var canvasWidth = 300;

    var container = document.getElementById('qrCanvasContainer');
    var frameWrapper = document.getElementById('qrFrameWrapper');
    var frameLabel = document.getElementById('qrFrameLabel');
    var previewText = document.getElementById('previewContentText');

    var fgColor = document.getElementById('fgColor');
    var fgColorText = document.getElementById('fgColorText');
    var bgColor = document.getElementById('bgColor');
    var bgColorText = document.getElementById('bgColorText');
    var gradStart = document.getElementById('gradStart');
    var gradStartText = document.getElementById('gradStartText');
    var gradEnd = document.getElementById('gradEnd');
    var gradEndText = document.getElementById('gradEndText');
    var eyeBorderColor = document.getElementById('eyeBorderColor');
    var eyeBorderColorText = document.getElementById('eyeBorderColorText');
    var eyeCenterColor = document.getElementById('eyeCenterColor');
    var eyeCenterColorText = document.getElementById('eyeCenterColorText');

    var gradientToggle = document.getElementById('gradientToggle');
    var gradOptions = document.getElementById('gradientOptions');
    var showFrame = document.getElementById('showFrame');
    var frameText = document.getElementById('frameText');
    var fontSize = document.getElementById('fontSize');
    var frameColor = document.getElementById('frameColor');
    var frameColorText = document.getElementById('frameColorText');
    var textColor = document.getElementById('textColor');
    var textColorText = document.getElementById('textColorText');
    var qrMargin = document.getElementById('qrMargin');
    var logoUpload = document.getElementById('logoUpload');
    var removeLogoBtn = document.getElementById('removeLogoBtn');

    function syncColor(picker, text) {
        picker.addEventListener('input', function() { text.value = this.value; render(); });
        text.addEventListener('input', function() { if (/^#[0-9a-f]{6}$/i.test(this.value)) { picker.value = this.value; render(); } });
    }
    syncColor(fgColor, fgColorText);
    syncColor(bgColor, bgColorText);
    syncColor(gradStart, gradStartText);
    syncColor(gradEnd, gradEndText);
    syncColor(eyeBorderColor, eyeBorderColorText);
    syncColor(eyeCenterColor, eyeCenterColorText);
    syncColor(frameColor, frameColorText);
    syncColor(textColor, textColorText);

    gradientToggle.addEventListener('change', function() {
        gradOptions.style.display = this.checked ? 'block' : 'none';
        render();
    });

    var typeBtns = document.querySelectorAll('.qr-type-btn');
    typeBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            typeBtns.forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            currentContentType = this.getAttribute('data-type');
            document.querySelectorAll('.qr-content-fields').forEach(function(f) { f.classList.remove('active'); });
            var target = document.querySelector('.qr-content-fields[data-for="' + currentContentType + '"]');
            if (target) target.classList.add('active');
            render();
        });
    });
    function getEncodedContent() {
        var type = currentContentType;
        var v = function(id) { var el = document.getElementById(id); return el ? el.value : ''; };

        switch (type) {
            case 'url': return v('fieldUrl');
            case 'text': return v('fieldText');
            case 'email': return 'mailto:' + v('fieldEmail') + '?subject=' + encodeURIComponent(v('fieldEmailSubject')) + '&body=' + encodeURIComponent(v('fieldEmailBody'));
            case 'call': return 'tel:' + v('fieldCall');
            case 'sms': return 'sms:' + v('fieldSmsPhone') + '?body=' + encodeURIComponent(v('fieldSmsMsg'));
            case 'whatsapp': return 'https://wa.me/' + v('fieldWaPhone').replace(/[^0-9]/g,'') + '?text=' + encodeURIComponent(v('fieldWaMsg'));
            case 'wifi': return 'WIFI:T:' + v('fieldWifiType') + ';S:' + v('fieldWifiSsid') + ';P:' + v('fieldWifiPass') + ';;';
            case 'vcard': return 'BEGIN:VCARD\nVERSION:3.0\nFN:' + v('fieldVcName') + '\nORG:' + v('fieldVcOrg') + '\nTITLE:' + v('fieldVcTitle') + '\nTEL:' + v('fieldVcPhone') + '\nEMAIL:' + v('fieldVcEmail') + '\nURL:' + v('fieldVcUrl') + '\nEND:VCARD';
            case 'event': return 'BEGIN:VEVENT\nSUMMARY:' + v('fieldEvTitle') + '\nDTSTART:' + v('fieldEvStart').replace(/[-:]/g,'') + '00\nDTEND:' + v('fieldEvEnd').replace(/[-:]/g,'') + '00\nLOCATION:' + v('fieldEvLocation') + '\nEND:VEVENT';
            default: return '';
        }
    }

    function buildOptions() {
        var data = getEncodedContent();
        if (data) previewText.textContent = data.length > 60 ? data.substring(0, 60) + '...' : data;
        else previewText.textContent = '-';

        var dotsOpts = { color: fgColor.value };
        var dotTypeEl = document.querySelector('#dotStyleOptions .qr-style-btn.active');
        dotsOpts.type = dotTypeEl ? dotTypeEl.getAttribute('data-value') : 'square';

        if (gradientToggle.checked) {
            dotsOpts.gradient = {
                type: 'linear',
                rotation: 0,
                colorStops: [{ offset: 0, color: gradStart.value }, { offset: 1, color: gradEnd.value }]
            };
            delete dotsOpts.color;
        }

        var eyeBorderType = 'square';
        var eyeCenterType = 'dot';
        var eyeStyleEl = document.querySelector('#eyeStyleOptions .qr-style-btn.active');
        if (eyeStyleEl) {
            var ev = eyeStyleEl.getAttribute('data-value');
            if (ev === 'rounded') { eyeBorderType = 'extra-rounded'; eyeCenterType = 'square'; }
            else if (ev === 'circle') { eyeBorderType = 'dot'; eyeCenterType = 'dot'; }
            else { eyeBorderType = 'square'; eyeCenterType = 'square'; }
        }

        var opts = {
            width: canvasWidth,
            height: canvasWidth,
            type: 'canvas',
            data: data,
            margin: 0,
            qrOptions: { typeNumber: 0, mode: 'Byte', errorCorrectionLevel: 'H' },
            imageOptions: { hideBackgroundDots: true, imageSize: 0.4, margin: 6 },
            dotsOptions: dotsOpts,
            cornersSquareOptions: { type: eyeBorderType, color: eyeBorderColor.value },
            cornersDotOptions: { type: eyeCenterType, color: eyeCenterColor.value },
            backgroundOptions: { color: bgColor.value }
        };

        if (logoDataURL) opts.image = logoDataURL;

        return opts;
    }
    function getFrameType() {
        var el = document.querySelector('#frameStyleOptions .qr-style-btn.active');
        return el ? el.getAttribute('data-value') : 'panel';
    }
    function updateFrame() {
        var show = showFrame.checked;
        var margin = parseInt(qrMargin.value) || 0;
        var type = show ? getFrameType() : 'none';

        frameLabel.style.display = (type !== 'none') ? 'block' : 'none';
        frameLabel.textContent = (type !== 'none') ? (frameText.value || 'SCAN ME') : '';
        frameLabel.style.color = (type !== 'none') ? textColor.value : '';
        frameLabel.style.fontSize = fontSize.value + 'px';

        var fc = frameColor.value;

        frameWrapper.style.padding = '0';
        frameWrapper.style.background = 'transparent';
        frameWrapper.style.borderRadius = '0';
        frameWrapper.style.boxShadow = 'none';
        frameWrapper.style.border = 'none';
        frameLabel.style.background = 'transparent';
        frameLabel.style.marginTop = '0';

        if (type === 'panel') {
            frameWrapper.style.padding = margin + 'px';
            frameWrapper.style.paddingBottom = '0';
            frameWrapper.style.background = fc;
            frameWrapper.style.borderRadius = '10px';
            frameWrapper.style.boxShadow = '0 2px 16px rgba(0,0,0,0.08)';
            frameLabel.style.background = fc;
        } else if (type === 'outline') {
            frameWrapper.style.padding = margin + 'px';
            frameWrapper.style.paddingBottom = '0';
            frameWrapper.style.border = '2px solid ' + fc;
            frameWrapper.style.borderRadius = '10px';
            frameLabel.style.background = fc;
            frameLabel.style.color = '#fff';
            frameLabel.style.marginTop = '6px';
        } else if (type === 'shadow') {
            frameWrapper.style.padding = margin + 'px';
            frameWrapper.style.paddingBottom = '0';
            frameWrapper.style.boxShadow = '0 8px 30px rgba(0,0,0,0.15)';
            frameWrapper.style.borderRadius = '10px';
            frameLabel.style.background = fc;
            frameLabel.style.color = textColor.value;
        } else if (type === 'bar') {
            frameWrapper.style.padding = margin + 'px';
            frameWrapper.style.paddingBottom = '0';
            frameLabel.style.background = fc;
        }
    }

    function render() {
        updateFrame();
        var opts = buildOptions();
        if (!opts.data) {
            container.innerHTML = '<div style="width:' + canvasWidth + 'px;height:' + canvasWidth + 'px;display:flex;align-items:center;justify-content:center;background:#f0f0f0;border-radius:8px;color:#999;font-size:14px">{{ __('translate.Enter content') }}</div>';
            return;
        }

        try {
            if (!qrInstance) {
                container.innerHTML = '';
                qrInstance = new QRCodeStyling(opts);
                qrInstance.append(container);
            } else {
                qrInstance.update(opts);
            }
        } catch (e) {
            container.innerHTML = '<div style="width:' + canvasWidth + 'px;height:' + canvasWidth + 'px;display:flex;align-items:center;justify-content:center;background:#f0f0f0;border-radius:8px;color:#999;font-size:14px">{{ __("translate.Error") }}</div>';
        }
    }

    function initStyleButtons(containerId, onChange) {
        var btns = document.querySelectorAll('#' + containerId + ' .qr-style-btn');
        btns.forEach(function(b) {
            b.addEventListener('click', function() {
                btns.forEach(function(x) { x.classList.remove('active'); });
                this.classList.add('active');
                if (onChange) onChange();
            });
        });
    }
    initStyleButtons('dotStyleOptions', render);
    initStyleButtons('eyeStyleOptions', render);
    initStyleButtons('frameStyleOptions', render);

    logoUpload.addEventListener('change', function(e) {
        var file = e.target.files[0];
        if (!file) return;
        var reader = new FileReader();
        reader.onload = function(ev) {
            logoDataURL = ev.target.result;
            removeLogoBtn.style.display = 'inline-flex';
            render();
        };
        reader.readAsDataURL(file);
    });

    removeLogoBtn.addEventListener('click', function() {
        logoDataURL = null;
        logoUpload.value = '';
        removeLogoBtn.style.display = 'none';
        render();
    });

    document.querySelectorAll('.qr-accordion-trigger').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            var body = document.getElementById(targetId);
            if (!body) return;
            var isOpen = body.style.display === 'block';
            body.style.display = isOpen ? 'none' : 'block';
            this.classList.toggle('active', !isOpen);
        });
    });

    var contentInputs = document.querySelectorAll('#contentInputCard input, #contentInputCard textarea, #contentInputCard select');
    contentInputs.forEach(function(el) {
        el.addEventListener('input', render);
        el.addEventListener('change', render);
    });

    showFrame.addEventListener('change', render);
    frameText.addEventListener('input', render);
    fontSize.addEventListener('change', render);
    qrMargin.addEventListener('change', render);

    function updateCanvasSize() {
        var wrap = document.querySelector('.qr-preview-canvas-wrap');
        if (!wrap) return;
        var w = wrap.clientWidth - 48;
        canvasWidth = Math.min(400, Math.max(200, w));
        if (qrInstance) {
            try { qrInstance.update({ width: canvasWidth, height: canvasWidth }); }
            catch(e) { qrInstance = null; render(); }
        }
    }

    var resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(updateCanvasSize, 200);
    });

    document.getElementById('downloadPngBtn').addEventListener('click', function() {
        if (!qrInstance) { alert('{{ __('translate.Please enter content first') }}'); return; }
        try { qrInstance.download({ extension: 'png', name: 'custom-qrcode' }); }
        catch(e) { alert('{{ __('translate.QR code not ready') }}'); }
    });
    document.getElementById('saveForm').addEventListener('submit', function(e) {
        var data = getEncodedContent();
        if (!data) { e.preventDefault(); alert('{{ __('translate.Please enter content for the QR code') }}'); return false; }
        document.getElementById('saveContent').value = data;
        document.getElementById('saveFg').value = fgColor.value;
        document.getElementById('saveBg').value = bgColor.value;
        document.getElementById('saveSize').value = canvasWidth;
        var dotEl = document.querySelector('#dotStyleOptions .qr-style-btn.active');
        document.getElementById('saveDotStyle').value = dotEl ? dotEl.getAttribute('data-value') : 'square';
        var eyeEl = document.querySelector('#eyeStyleOptions .qr-style-btn.active');
        document.getElementById('saveEyeStyle').value = eyeEl ? eyeEl.getAttribute('data-value') : 'square';
        document.getElementById('saveEyeBorder').value = eyeBorderColor.value;
        document.getElementById('saveEyeCenter').value = eyeCenterColor.value;
        document.getElementById('saveGradientEnabled').value = gradientToggle.checked ? '1' : '0';
        document.getElementById('saveGradientStart').value = gradStart.value;
        document.getElementById('saveGradientEnd').value = gradEnd.value;
        var frameEl = document.querySelector('#frameStyleOptions .qr-style-btn.active');
        document.getElementById('saveFrameStyle').value = frameEl ? frameEl.getAttribute('data-value') : 'panel';
        document.getElementById('saveFrameText').value = frameText.value;
        document.getElementById('saveFrameColor').value = frameColor.value;
        document.getElementById('saveFrameTextColor').value = textColor.value;
        document.getElementById('saveFrameMargin').value = qrMargin.value;
        document.getElementById('saveFrameFontSize').value = fontSize.value;
    });

    setTimeout(function() {
        updateCanvasSize();
        render();
    }, 100);
})();
</script>

@if($general_setting->recaptcha_status == 1)
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

<style>
/* ===== LAYOUT ===== */
.qr-generator-layout {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 30px;
    align-items: start;
}
@media (max-width: 991px) {
    .qr-generator-layout {
        grid-template-columns: 1fr;
    }
    .qr-preview-panel {
        position: static !important;
    }
}
.qr-control-panel {
    min-width: 0;
}
.qr-preview-panel {
    min-width: 0;
    position: sticky;
    top: 100px;
    align-self: start;
}

/* ===== CARDS ===== */
.qr-card {
    background: var(--white-color);
    border-radius: 12px;
    padding: 24px 28px;
    margin-bottom: 20px;
    box-shadow: 0 2px 12px rgba(10,22,94,0.06);
}
.qr-card-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--heading-color);
    margin-bottom: 16px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--light-color1);
}
.qr-field {
    margin-bottom: 12px;
}
.qr-field:last-child { margin-bottom: 0; }
.qr-field label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--heading-color);
    margin-bottom: 5px;
}
.qr-field input[type="text"],
.qr-field input[type="tel"],
.qr-field input[type="email"],
.qr-field input[type="url"],
.qr-field input[type="datetime-local"],
.qr-field textarea,
.qr-field select {
    width: 100%;
    height: 40px;
    padding: 6px 12px;
    border: 1px solid var(--light-color2);
    border-radius: 8px;
    font-size: 14px;
    color: var(--heading-color);
    background: var(--white-color);
    transition: border-color 0.2s;
    border-bottom: 1px solid var(--light-color2) !important;
    box-sizing: border-box;
}
.qr-field textarea { height: auto; padding-top: 10px; }
.qr-field input:focus,
.qr-field textarea:focus,
.qr-field select:focus {
    border-color: var(--accent-color) !important;
    outline: none;
    box-shadow: 0 0 0 3px rgba(43,77,255,0.08);
}
.qr-field input[type="checkbox"] {
    width: auto;
    height: auto;
    margin-right: 6px;
    accent-color: var(--accent-color);
}

/* ===== COLOR INPUTS ===== */
.qr-color-input {
    display: flex;
    gap: 6px;
    align-items: center;
}
.qr-color-input input[type="color"] {
    width: 40px;
    height: 40px;
    padding: 3px;
    border: 1px solid var(--light-color2);
    border-radius: 8px;
    cursor: pointer;
    background: none;
    flex-shrink: 0;
}
.qr-color-input input[type="text"] {
    flex: 1;
    min-width: 0;
    font-family: monospace;
    height: 36px;
}

/* ===== CONTENT TYPE GRID ===== */
.qr-content-type-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}
.qr-type-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    padding: 10px 6px;
    border: 1px solid var(--light-color2);
    border-radius: 10px;
    background: var(--white-color);
    cursor: pointer;
    transition: all 0.2s;
    font-size: 11px;
    font-weight: 600;
    color: var(--body-color);
}
.qr-type-btn i { font-size: 20px; }
.qr-type-btn:hover { border-color: var(--accent-color); color: var(--accent-color); }
.qr-type-btn.active { border-color: var(--accent-color); background: rgba(43,77,255,0.06); color: var(--accent-color); }
.qr-content-fields { display: none; }
.qr-content-fields.active { display: block; }

/* ===== ACCORDION ===== */
.qr-accordion-trigger {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    padding: 0;
    border: none;
    background: none;
    font-size: 16px;
    font-weight: 700;
    color: var(--heading-color);
    cursor: pointer;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--light-color1);
    margin-bottom: 16px;
}
.qr-accordion-trigger i { transition: transform 0.2s; font-size: 18px; }
.qr-accordion-trigger.active i { transform: rotate(180deg); }
.qr-accordion-body { display: none; }
.qr-accordion-body[style*="block"] { display: block; }
.qr-section-group { margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--light-color1); }
.qr-section-group:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
.qr-section-label { font-size: 14px; font-weight: 700; color: var(--heading-color); margin: 0 0 12px; }

/* ===== STYLE BUTTONS ===== */
.qr-style-options {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.qr-style-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border: 1px solid var(--light-color2);
    border-radius: 8px;
    background: var(--white-color);
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
    color: var(--body-color);
    transition: all 0.2s;
}
.qr-style-btn:hover { border-color: var(--accent-color); color: var(--accent-color); }
.qr-style-btn.active { border-color: var(--accent-color); background: rgba(43,77,255,0.06); color: var(--accent-color); }
.qr-sp {
    width: 14px;
    height: 14px;
    display: inline-block;
    border: 1px solid var(--light-color2);
    border-radius: 2px;
    flex-shrink: 0;
}
.qr-sp-rounded { border-radius: 4px; }
.qr-sp-dots { border-radius: 50%; }
.qr-sp-classy { border-radius: 2px; background: linear-gradient(135deg,var(--light-color2) 0%,var(--body-color) 100%); }
.qr-sp-erounded { border-radius: 6px; }

/* ===== FILE INPUT ===== */
.qr-file-label {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border: 2px dashed var(--light-color2);
    border-radius: 8px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    color: var(--body-color);
    transition: all 0.2s;
}
.qr-file-label:hover { border-color: var(--accent-color); color: var(--accent-color); }
.qr-file-label input { display: none; }
.qr-btn-sm {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    border: 1px solid #dc3545;
    border-radius: 6px;
    background: #fff;
    color: #dc3545;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}
.qr-btn-sm:hover { background: #dc3545; color: #fff; }

/* ===== PREVIEW ===== */
.qr-preview-canvas-wrap {
    background: #f8f9fc;
    border-radius: 12px;
    padding: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 280px;
}
.qr-preview-canvas-wrap canvas {
    max-width: 100%;
    height: auto;
    border-radius: 4px;
}
.qr-frame-wrapper {
    position: relative;
    display: inline-block;
    text-align: center;
}
.qr-frame-label {
    display: block;
    font-weight: 800;
    letter-spacing: 3px;
    text-transform: uppercase;
    white-space: nowrap;
    font-family: 'Sora', sans-serif;
    user-select: none;
    padding: 8px 18px 12px;
    border-radius: 0 0 10px 10px;
    line-height: 1.2;
}

/* ===== BUTTONS ===== */
.qr-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 12px 24px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    border: none;
    line-height: 1.2;
}
.qr-btn--primary {
    background: var(--accent-color);
    color: #fff;
}
.qr-btn--primary:hover { background: #1a3dcc; color: #fff; }
.qr-btn--outline {
    background: #fff;
    color: var(--heading-color);
    border: 1px solid var(--light-color2);
}
.qr-btn--outline:hover { border-color: var(--accent-color); color: var(--accent-color); }
</style>
@endsection


