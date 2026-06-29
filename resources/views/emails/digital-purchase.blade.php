<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>{{ __('translate.Your Download is Ready') }}</title>
<style>
body{font-family:'Segoe UI',sans-serif;background:#f4f6f9;margin:0;padding:0;color:#333}
.container{max-width:600px;margin:40px auto;background:#fff;border-radius:12px;box-shadow:0 2px 24px rgba(0,0,0,0.06);overflow:hidden}
.header{background:linear-gradient(135deg,#2b4dff,#1a3dcc);padding:32px;text-align:center}
.header h1{color:#fff;margin:0;font-size:22px;font-weight:700}
.body-content{padding:32px}
.body-content p{font-size:15px;line-height:1.6;margin:0 0 16px}
.download-btn{display:inline-block;background:#2b4dff;color:#fff;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:700;font-size:16px;margin:8px 0}
.download-btn:hover{background:#1a3dcc}
.info-box{background:#f8f9fc;border-radius:8px;padding:16px;margin:16px 0;font-size:14px;border:1px solid #e9ecef}
.info-box strong{display:block;margin-bottom:4px;color:#555}
.footer{text-align:center;padding:24px;font-size:13px;color:#888;border-top:1px solid #eee}
</style>
</head>
<body>
<div class="container">
<div class="header"><h1>{{ __('translate.Your Purchase is Complete!') }}</h1></div>
<div class="body-content">
<p>{{ __('translate.Hi') }} <strong>{{ $user_name }}</strong>,</p>
<p>{{ __('translate.Thank you for purchasing') }} <strong>{{ $product_name }}</strong>! {{ __('translate.Your download is ready.') }}</p>
<a href="{{ $download_url }}" class="download-btn">{{ __('translate.Download Now') }}</a>
@if($license_key)
<div class="info-box">
<strong>{{ __('translate.License Key') }}:</strong>
<div style="font-family:monospace;font-size:16px;background:#fff;padding:8px 12px;border-radius:4px;border:1px dashed #ccc;margin-top:6px;word-break:break-all">{{ $license_key }}</div>
</div>
@endif
@if($support_expiry)
<div class="info-box">
<strong>{{ __('translate.Updates & Support') }}:</strong> {{ __('translate.Available until') }} {{ $support_expiry }}
</div>
@endif
<p style="font-size:13px;color:#888">{{ __('translate.This download link is unique to your purchase. Do not share it.') }}</p>
</div>
<div class="footer">&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('translate.All rights reserved.') }}</div>
</div>
</body>
</html>
