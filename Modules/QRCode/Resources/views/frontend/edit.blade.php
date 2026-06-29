@extends('master_layout')
@section('title')
    <title>{{ $pageTitle ?? '' }} - {{ config('app.name') }}</title>
@endsection
@section('new-layout')
<div class="optech-breadcrumb" style="background-image: url({{ asset($general_setting->breadcrumb_image) }})">
    <div class="container">
        <h1 class="post__title">{{ __($pageTitle) }}</h1>
        <nav class="breadcrumbs">
            <ul>
                <li><a href="{{ route('home') }}">{{ __('translate.Home') }}</a></li>
                <li><a href="{{ route('qrcode.create') }}">{{ __('translate.QR Code Generator') }}</a></li>
                <li><a href="{{ route('user.qr-codes') }}">{{ __('translate.My QR Codes') }}</a></li>
                <li aria-current="page">#{{ $qrCode->id }}</li>
            </ul>
        </nav>
    </div>
</div>

<div class="section optech-section-padding2">
    <div class="container">
        <form action="{{ route('qrcode.update', $qrCode->id) }}" method="POST" id="editForm">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-7">
                    <div class="qr-card">
                        <h4 class="qr-card-title">{{ __('translate.Content') }}</h4>
                        <div class="qr-field">
                            <label>{{ __('translate.URL, Text, Phone, Email...') }}</label>
                            <textarea name="content" id="qrContent" rows="3" placeholder="https://example.com or any text..." style="resize:vertical;min-height:80px">{{ old('content', $qrCode->content) }}</textarea>
                        </div>
                    </div>

                    <div class="qr-card">
                        <h4 class="qr-card-title">{{ __('translate.Design') }}</h4>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="qr-field">
                                    <label>{{ __('translate.Foreground') }}</label>
                                    <div class="qr-color-input">
                                        <input type="color" name="foreground_color" id="fgColor" value="{{ $qrCode->foreground_color }}">
                                        <input type="text" id="fgColorText" value="{{ $qrCode->foreground_color }}" maxlength="7">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="qr-field">
                                    <label>{{ __('translate.Background') }}</label>
                                    <div class="qr-color-input">
                                        <input type="color" name="background_color" id="bgColor" value="{{ $qrCode->background_color }}">
                                        <input type="text" id="bgColorText" value="{{ $qrCode->background_color }}" maxlength="7">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="qr-field">
                                    <label>{{ __('translate.Size') }}</label>
                                    <select name="size" id="qrSize">
                                        <option value="200" {{ $qrCode->size == 200 ? 'selected' : '' }}>200 x 200</option>
                                        <option value="250" {{ $qrCode->size == 250 ? 'selected' : '' }}>250 x 250</option>
                                        <option value="300" {{ $qrCode->size == 300 ? 'selected' : '' }}>300 x 300</option>
                                        <option value="400" {{ $qrCode->size == 400 ? 'selected' : '' }}>400 x 400</option>
                                        <option value="500" {{ $qrCode->size == 500 ? 'selected' : '' }}>500 x 500</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Preserve design fields as hidden -->
                    <input type="hidden" name="dot_style" value="{{ $qrCode->dot_style ?? 'square' }}">
                    <input type="hidden" name="eye_style" value="{{ $qrCode->eye_style ?? 'square' }}">
                    <input type="hidden" name="eye_border_color" value="{{ $qrCode->eye_border_color ?? '#000000' }}">
                    <input type="hidden" name="eye_center_color" value="{{ $qrCode->eye_center_color ?? '#000000' }}">
                    <input type="hidden" name="gradient_enabled" value="{{ $qrCode->gradient_enabled ? '1' : '0' }}">
                    <input type="hidden" name="gradient_start" value="{{ $qrCode->gradient_start ?? '#FF0000' }}">
                    <input type="hidden" name="gradient_end" value="{{ $qrCode->gradient_end ?? '#0000FF' }}">
                    <input type="hidden" name="frame_style" value="{{ $qrCode->frame_style ?? 'panel' }}">
                    <input type="hidden" name="frame_text" value="{{ $qrCode->frame_text ?? 'SCAN ME' }}">
                    <input type="hidden" name="frame_color" value="{{ $qrCode->frame_color ?? '#000000' }}">
                    <input type="hidden" name="frame_text_color" value="{{ $qrCode->frame_text_color ?? '#FFFFFF' }}">
                    <input type="hidden" name="frame_margin" value="{{ $qrCode->frame_margin ?? 2 }}">
                    <input type="hidden" name="frame_font_size" value="{{ $qrCode->frame_font_size ?? 14 }}">

                    <div class="qr-card">
                        <div style="display:flex;gap:12px;flex-wrap:wrap">
                            <button type="submit" class="qr-btn qr-btn--primary">
                                <i class="ri-save-line"></i> {{ __('translate.Save Changes') }}
                            </button>
                            <a href="{{ route('qrcode.show', $qrCode->id) }}" class="qr-btn qr-btn--outline">
                                <i class="ri-close-line"></i> {{ __('translate.Cancel') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="qr-preview-sticky">
                        <div class="qr-card" style="text-align:center">
                            <h4 class="qr-card-title" style="text-align:left">{{ __('translate.Preview') }}</h4>
                            <div class="qr-preview-canvas-wrap">
                                <div class="qr-frame-wrapper" id="qrFrameWrapper">
                                    <div id="qrCanvasContainer"></div>
                                    <div id="qrFrameLabel" class="qr-frame-label">{{ __('translate.SCAN ME') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('assets/js/qrcode-styling.min.js') }}"></script>
<script>
"use strict";
var container = document.getElementById('qrCanvasContainer');
var frameWrapper = document.getElementById('qrFrameWrapper');
var frameLabel = document.getElementById('qrFrameLabel');
var content = document.getElementById('qrContent');
var fg = document.getElementById('fgColor');
var fgTxt = document.getElementById('fgColorText');
var bg = document.getElementById('bgColor');
var bgTxt = document.getElementById('bgColorText');
var size = document.getElementById('qrSize');

var qrInstance = null;

// Saved design values
var saved = {
    dotStyle: {!! json_encode($qrCode->dot_style ?? 'square') !!},
    eyeStyle: {!! json_encode($qrCode->eye_style ?? 'square') !!},
    eyeBorder: {!! json_encode($qrCode->eye_border_color ?? '#000000') !!},
    eyeCenter: {!! json_encode($qrCode->eye_center_color ?? '#000000') !!},
    gradEnabled: {{ $qrCode->gradient_enabled ? 'true' : 'false' }},
    gradStart: {!! json_encode($qrCode->gradient_start ?? '#FF0000') !!},
    gradEnd: {!! json_encode($qrCode->gradient_end ?? '#0000FF') !!},
    frameStyle: {!! json_encode($qrCode->frame_style ?? 'panel') !!},
    frameText: {!! json_encode($qrCode->frame_text ?? 'SCAN ME') !!},
    frameColor: {!! json_encode($qrCode->frame_color ?? '#000000') !!},
    frameTextColor: {!! json_encode($qrCode->frame_text_color ?? '#FFFFFF') !!},
    frameMargin: parseInt("{{ $qrCode->frame_margin ?? 2 }}") || 2,
    frameFontSize: parseInt("{{ $qrCode->frame_font_size ?? 14 }}") || 14
};

fg.addEventListener('input', function() { fgTxt.value = this.value; gen(); });
fgTxt.addEventListener('input', function() { if (/^#[0-9a-f]{6}$/i.test(this.value)) { fg.value = this.value; gen(); } });
bg.addEventListener('input', function() { bgTxt.value = this.value; gen(); });
bgTxt.addEventListener('input', function() { if (/^#[0-9a-f]{6}$/i.test(this.value)) { bg.value = this.value; gen(); } });
content.addEventListener('input', gen);
size.addEventListener('change', gen);

function updateFrameUI() {
    var show = saved.frameStyle !== 'none' && saved.frameStyle !== '';
    frameLabel.style.display = show ? 'block' : 'none';
    frameLabel.textContent = show ? (saved.frameText || 'SCAN ME') : '';
    frameLabel.style.fontSize = saved.frameFontSize + 'px';
    frameLabel.style.color = saved.frameTextColor;
    frameLabel.style.background = 'transparent';
    frameLabel.style.marginTop = '0';

    frameWrapper.style.padding = '0';
    frameWrapper.style.background = 'transparent';
    frameWrapper.style.borderRadius = '0';
    frameWrapper.style.boxShadow = 'none';
    frameWrapper.style.border = 'none';

    if (!show) return;
    var m = saved.frameMargin;
    var fc = saved.frameColor;

    if (saved.frameStyle === 'panel') {
        frameWrapper.style.padding = m + 'px 4px 0';
        frameWrapper.style.background = fc;
        frameWrapper.style.borderRadius = '10px';
        frameWrapper.style.boxShadow = '0 2px 16px rgba(0,0,0,0.08)';
        frameLabel.style.background = fc;
    } else if (saved.frameStyle === 'outline') {
        frameWrapper.style.padding = m + 'px 4px 0';
        frameWrapper.style.border = '2px solid ' + fc;
        frameWrapper.style.borderRadius = '10px';
        frameLabel.style.background = fc;
        frameLabel.style.color = '#fff';
        frameLabel.style.marginTop = '6px';
    } else if (saved.frameStyle === 'shadow') {
        frameWrapper.style.padding = m + 'px 4px 0';
        frameWrapper.style.boxShadow = '0 8px 30px rgba(0,0,0,0.15)';
        frameWrapper.style.borderRadius = '10px';
        frameLabel.style.background = fc;
        frameLabel.style.color = saved.frameTextColor;
    } else if (saved.frameStyle === 'bar') {
        frameWrapper.style.padding = m + 'px 4px 0';
        frameLabel.style.background = fc;
    }
}

function getEyeTypes(style) {
    if (style === 'rounded') return { border: 'extra-rounded', center: 'square' };
    if (style === 'circle') return { border: 'dot', center: 'dot' };
    return { border: 'square', center: 'square' };
}

function gen() {
    var s = content.value.trim();
    var w = parseInt(size.value) || 200;
    container.innerHTML = '';

    if (!s) {
        container.innerHTML = '<div style="width:'+w+'px;height:'+w+'px;display:flex;align-items:center;justify-content:center;background:#f0f0f0;border-radius:8px;color:#999;font-size:14px">{{ __("translate.Enter content") }}</div>';
        updateFrameUI();
        return;
    }

    updateFrameUI();

    var dotsOpts = { color: fg.value };
    dotsOpts.type = saved.dotStyle;
    if (saved.gradEnabled) {
        dotsOpts.gradient = { type: 'linear', rotation: 0, colorStops: [{ offset: 0, color: saved.gradStart }, { offset: 1, color: saved.gradEnd }] };
        delete dotsOpts.color;
    }

    var eyes = getEyeTypes(saved.eyeStyle);

    try {
        if (!qrInstance) {
            qrInstance = new QRCodeStyling({
                width: w, height: w, type: 'canvas', data: s, margin: 0,
                qrOptions: { typeNumber: 0, mode: 'Byte', errorCorrectionLevel: 'H' },
                imageOptions: { hideBackgroundDots: true, imageSize: 0.35, margin: 4, crossOrigin: 'anonymous' },
                dotsOptions: dotsOpts,
                cornersSquareOptions: { type: eyes.border, color: saved.eyeBorder },
                cornersDotOptions: { type: eyes.center, color: saved.eyeCenter },
                backgroundOptions: { color: bg.value }
            });
            qrInstance.append(container);
        } else {
            qrInstance.update({
                width: w, height: w, data: s,
                dotsOptions: dotsOpts,
                cornersSquareOptions: { type: eyes.border, color: saved.eyeBorder },
                cornersDotOptions: { type: eyes.center, color: saved.eyeCenter },
                backgroundOptions: { color: bg.value }
            });
        }
    } catch(e) {
        container.innerHTML = '<div style="width:'+w+'px;height:'+w+'px;display:flex;align-items:center;justify-content:center;background:#f0f0f0;border-radius:8px;color:#999;font-size:14px">{{ __("translate.Error") }}</div>';
        qrInstance = null;
    }
}

document.getElementById('editForm').addEventListener('submit', function() {
    if (!content.value.trim()) { alert('{{ __('translate.Please enter content for the QR code') }}'); return false; }
});

gen();
</script>

<style>
.qr-card {
    background: var(--white-color);
    border-radius: 12px;
    padding: 24px 28px;
    margin-bottom: 20px;
    box-shadow: 0 2px 12px rgba(10, 22, 94, 0.06);
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
    margin-bottom: 14px;
}
.qr-field:last-child {
    margin-bottom: 0;
}
.qr-field label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--heading-color);
    margin-bottom: 5px;
}
.qr-field input[type="text"],
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
    box-sizing:border-box;
}
.qr-field textarea {
    height: auto;
    padding-top: 10px;
}
.qr-field input:focus,
.qr-field textarea:focus,
.qr-field select:focus {
    border-color: var(--accent-color) !important;
    outline: none;
    box-shadow: 0 0 0 3px rgba(43, 77, 255, 0.08);
}
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
}
.qr-preview-sticky {
    position: sticky;
    top: 100px;
}
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
    display: block;
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
.qr-btn--primary:hover {
    background: #1a3dcc;
    color: #fff;
}
.qr-btn--outline {
    background: #fff;
    color: var(--heading-color);
    border: 1px solid var(--light-color2);
}
.qr-btn--outline:hover {
    border-color: var(--accent-color);
    color: var(--accent-color);
}
@media (max-width: 991px) {
    .qr-preview-sticky { position: static; margin-top: 24px; }
}
</style>
@endsection
