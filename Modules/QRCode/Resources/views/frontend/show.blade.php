@extends('master_layout')
@section('title')
    <title>{{ $pageTitle ?? '' }} - {{ config('app.name') }}</title>
@endsection
@section('new-layout')
<div class="optech-breadcrumb" style="background-image: url({{ asset($general_setting->breadcrumb_image ?? '') }})">
    <div class="container">
        <h1 class="post__title">{{ __('translate.QR Code') }}</h1>
        <nav class="breadcrumbs">
            <ul>
                <li><a href="{{ route('home') }}">{{ __('translate.Home') }}</a></li>
                <li><a href="{{ route('qrcode.create') }}">{{ __('translate.QR Code Generator') }}</a></li>
                <li aria-current="page">#{{ $qrCode->id }}</li>
            </ul>
        </nav>
    </div>
</div>

<div class="section optech-section-padding2">
    <div class="container">
        @if ($qrCode->scheduled_deletion_at)
        <div class="text-center mb-4">
            @if ($qrCode->user_id)
                <span class="qr-deletion-notice qr-deletion-notice--user">
                    <i class="ri-time-line"></i>
                    {{ __('translate.This QR code will be automatically deleted on') }} {{ $qrCode->scheduled_deletion_at->format('F d, Y') }}.
                </span>
            @else
                <span class="qr-deletion-notice qr-deletion-notice--guest">
                    <i class="ri-alert-line"></i>
                    {{ __('translate.Guest QR code — will be deleted in 24 hours.') }}
                    <a href="{{ route('user.register') }}">{{ __('translate.Sign up to save it for 3 months.') }}</a>
                </span>
            @endif
        </div>
        @endif

        <div class="qr-show-card">
            <div class="qr-show-canvas-wrap">
                <div class="qr-frame-wrapper" id="qrFrameWrapper">
                    <div id="qrDisplay"></div>
                    <div id="qrFrameLabel" class="qr-frame-label">{{ __('translate.SCAN ME') }}</div>
                </div>
            </div>

            <div class="qr-show-meta">
                <p class="qr-show-content">
                    <strong>{{ __('translate.Content') }}:</strong>
                    {{ $qrCode->content }}
                </p>
                <div class="qr-show-details">
                    <span>{{ __('translate.Size') }}: {{ $qrCode->size }}x{{ $qrCode->size }}</span>
                    <span>{{ __('translate.Dot Pattern') }}: {{ ucfirst($qrCode->dot_style ?? 'square') }}</span>
                    <span>{{ __('translate.Frame') }}: {{ ucfirst($qrCode->frame_style ?? 'panel') }}</span>
                </div>
            </div>

            <div class="qr-show-actions">
                <button class="qr-btn qr-btn--primary" id="downloadPngBtn">
                    <i class="ri-download-2-line"></i> {{ __('translate.PNG') }}
                </button>
                <button class="qr-btn qr-btn--primary" id="downloadJpegBtn">
                    <i class="ri-image-line"></i> {{ __('translate.JPEG') }}
                </button>
                <a href="{{ route('qrcode.create') }}" class="qr-btn qr-btn--outline">
                    <i class="ri-add-circle-line"></i> {{ __('translate.Create New') }}
                </a>
                @if ($canEdit)
                <a href="{{ route('qrcode.edit', $qrCode->id) }}" class="qr-btn qr-btn--outline">
                    <i class="ri-edit-line"></i> {{ __('translate.Edit') }}
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/qrcode-styling.min.js') }}"></script>
<script>
(function() {
    'use strict';
    var display = document.getElementById('qrDisplay');
    var frameWrapper = document.getElementById('qrFrameWrapper');
    var frameLabel = document.getElementById('qrFrameLabel');
    if (!display) return;

    var qrText = {!! json_encode($qrCode->content ?? 'Empty') !!};
    if (!qrText || qrText.trim() === '') qrText = 'Empty';

    var fg = {!! json_encode($qrCode->foreground_color ?? '#000000') !!};
    var bg = {!! json_encode($qrCode->background_color ?? '#FFFFFF') !!};
    var sz = parseInt("{{ $qrCode->size ?? 300 }}") || 300;
    var dotStyle = {!! json_encode($qrCode->dot_style ?? 'square') !!};
    var eyeStyle = {!! json_encode($qrCode->eye_style ?? 'square') !!};
    var eyeBorder = {!! json_encode($qrCode->eye_border_color ?? '#000000') !!};
    var eyeCenter = {!! json_encode($qrCode->eye_center_color ?? '#000000') !!};
    var gradEnabled = {!! $qrCode->gradient_enabled ? 'true' : 'false' !!};
    var gradStart = {!! json_encode($qrCode->gradient_start ?? '#FF0000') !!};
    var gradEnd = {!! json_encode($qrCode->gradient_end ?? '#0000FF') !!};
    var frameStyle = {!! json_encode($qrCode->frame_style ?? 'panel') !!};
    var frameText = {!! json_encode($qrCode->frame_text ?? 'SCAN ME') !!};
    var frameColor = {!! json_encode($qrCode->frame_color ?? '#000000') !!};
    var frameTextColor = {!! json_encode($qrCode->frame_text_color ?? '#FFFFFF') !!};
    var frameMargin = parseInt("{{ $qrCode->frame_margin ?? 2 }}") || 2;
    var frameFontSize = parseInt("{{ $qrCode->frame_font_size ?? 14 }}") || 14;

    // Map eye style to qr-code-styling eye properties
    var eyeBorderType = 'square', eyeCenterType = 'square';
    if (eyeStyle === 'rounded') { eyeBorderType = 'extra-rounded'; eyeCenterType = 'square'; }
    else if (eyeStyle === 'circle') { eyeBorderType = 'dot'; eyeCenterType = 'dot'; }
    else { eyeBorderType = 'square'; eyeCenterType = 'square'; }

    // Build QR options
    var dotsOpts = { color: fg };
    dotsOpts.type = dotStyle;
    if (gradEnabled) {
        dotsOpts.gradient = {
            type: 'linear', rotation: 0,
            colorStops: [{ offset: 0, color: gradStart }, { offset: 1, color: gradEnd }]
        };
        delete dotsOpts.color;
    }

    try {
        var qrInstance = new QRCodeStyling({
            width: sz, height: sz, type: 'canvas', data: qrText,
            margin: 0,
            qrOptions: { typeNumber: 0, mode: 'Byte', errorCorrectionLevel: 'H' },
            imageOptions: { hideBackgroundDots: true, imageSize: 0.35, margin: 4, crossOrigin: 'anonymous' },
            dotsOptions: dotsOpts,
            cornersSquareOptions: { type: eyeBorderType, color: eyeBorder },
            cornersDotOptions: { type: eyeCenterType, color: eyeCenter },
            backgroundOptions: { color: bg }
        });
        qrInstance.append(display);

        // Apply frame
        var show = frameStyle !== 'none';
        frameLabel.style.display = show ? 'block' : 'none';
        frameLabel.textContent = show ? (frameText || 'SCAN ME') : '';
        frameLabel.style.fontSize = frameFontSize + 'px';
        frameLabel.style.color = frameTextColor;
        frameLabel.style.background = 'transparent';

        frameWrapper.style.padding = '0';
        frameWrapper.style.background = 'transparent';
        frameWrapper.style.borderRadius = '0';
        frameWrapper.style.boxShadow = 'none';
        frameWrapper.style.border = 'none';

        if (frameStyle === 'panel') {
            frameWrapper.style.padding = frameMargin + 'px 4px 0';
            frameWrapper.style.background = frameColor;
            frameWrapper.style.borderRadius = '10px';
            frameWrapper.style.boxShadow = '0 2px 16px rgba(0,0,0,0.08)';
            frameLabel.style.background = frameColor;
        } else if (frameStyle === 'outline') {
            frameWrapper.style.padding = frameMargin + 'px 4px 0';
            frameWrapper.style.border = '2px solid ' + frameColor;
            frameWrapper.style.borderRadius = '10px';
            frameLabel.style.background = frameColor;
            frameLabel.style.color = '#fff';
            frameLabel.style.marginTop = '6px';
        } else if (frameStyle === 'shadow') {
            frameWrapper.style.padding = frameMargin + 'px 4px 0';
            frameWrapper.style.boxShadow = '0 8px 30px rgba(0,0,0,0.15)';
            frameWrapper.style.borderRadius = '10px';
            frameLabel.style.background = frameColor;
            frameLabel.style.color = frameTextColor;
        } else if (frameStyle === 'bar') {
            frameWrapper.style.padding = frameMargin + 'px 4px 0';
            frameLabel.style.background = frameColor;
        }

        function getCanvas() {
            var c = display.querySelector('canvas');
            if (c) return c;
            var imgs = display.querySelectorAll('img');
            return imgs.length > 0 ? imgs[0] : null;
        }

        function downloadQR(format) {
            var canvas = getCanvas();
            if (!canvas) { alert('{{ __('translate.QR code not ready') }}'); return; }
            try {
                qrInstance.download({ extension: format, name: 'qrcode-' + Date.now() });
            } catch(e) {
                // fallback if download fails
                var src = canvas.toDataURL ? canvas.toDataURL('image/' + (format === 'jpeg' ? 'jpeg' : 'png')) : canvas.src;
                var link = document.createElement('a');
                link.download = 'qrcode-' + Date.now() + '.' + format;
                link.href = src;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }

        document.getElementById('downloadPngBtn').addEventListener('click', function() { downloadQR('png'); });
        document.getElementById('downloadJpegBtn').addEventListener('click', function() { downloadQR('jpeg'); });

    } catch (e) {
        console.error("QR Generation failed: ", e);
        display.innerHTML = '<div style="width:' + sz + 'px;height:' + sz + 'px;display:flex;align-items:center;justify-content:center;background:#f0f0f0;border-radius:8px;color:#999;font-size:14px">{{ __("translate.QR code could not be generated") }}</div>';
    }
})();
</script>

<style>
.qr-deletion-notice {
    font-size: 13px;
    padding: 8px 20px;
    border-radius: 8px;
    display: inline-block;
}
.qr-deletion-notice--user {
    color: var(--body-color);
    background: var(--light-bg3);
}
.qr-deletion-notice--guest {
    color: #e67e22;
    background: #fff8e1;
}
.qr-deletion-notice--guest a {
    color: var(--accent-color);
    font-weight: 600;
    text-decoration: underline;
}
.qr-show-card {
    max-width: 600px;
    margin: 0 auto;
    background: var(--white-color);
    border-radius: 16px;
    box-shadow: 0 2px 24px rgba(10,22,94,0.08);
    padding: 40px;
    text-align: center;
}
.qr-show-canvas-wrap {
    display: flex;
    justify-content: center;
    align-items: center;
    background: #f8f9fc;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    min-height: 200px;
}
.qr-show-canvas-wrap canvas, .qr-show-canvas-wrap img {
    max-width: 100%;
    height: auto;
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
.qr-show-meta {
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid var(--light-color1);
}
.qr-show-content {
    font-size: 14px;
    color: var(--body-color);
    word-break: break-all;
    margin: 0;
}
.qr-show-content strong {
    color: var(--heading-color);
}
.qr-show-details {
    margin-top: 12px;
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
    font-size: 13px;
    color: var(--body-color);
}
.qr-show-actions {
    margin-top: 24px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: center;
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
</style>
@endsection
