@extends('master_layout')
@section('title')
    <title>{{ $pageTitle ?? '' }} - {{ config('app.name') }}</title>
@endsection
@section('new-layout')
<div class="optech-breadcrumb" style="background-image: url({{ asset($general_setting->breadcrumb_image) }})">
    <div class="container">
        <h1 class="post__title">{{ __('translate.Invoice') }} #{{ $invoice->invoice_number }}</h1>
        <nav class="breadcrumbs">
            <ul>
                <li><a href="{{ route('home') }}">{{ __('translate.Home') }}</a></li>
                <li><a href="{{ route('invoice.create') }}">{{ __('translate.Invoice Generator') }}</a></li>
                <li aria-current="page">#{{ $invoice->invoice_number }}</li>
            </ul>
        </nav>
    </div>
</div>

<div class="section optech-section-padding2">
    <div class="container">
        <div class="text-center mb-4">
            <button class="optech-invoice-print-btn" onclick="window.print()">
                <i class="ri-printer-line"></i> {{ __('translate.Print') }}
            </button>
            <button class="optech-invoice-print-btn" id="downloadPdfBtn" style="background:var(--heading-color)">
                <i class="ri-download-2-line"></i> {{ __('translate.Download PDF') }}
            </button>
            <a href="{{ route('invoice.create') }}" class="optech-invoice-new-btn">
                <i class="ri-add-circle-line"></i> {{ __('translate.Create New Invoice') }}
            </a>
            @if ($canEdit)
            <a href="{{ route('invoice.edit', $invoice->id) }}" class="optech-invoice-new-btn" style="border-color:var(--heading-color);color:var(--heading-color)">
                <i class="ri-edit-line"></i> {{ __('translate.Edit') }}
            </a>
            @endif
        </div>

        @if ($invoice->scheduled_deletion_at)
        <div class="text-center mb-4">
            @if ($invoice->user_id)
                <span style="font-size:13px;color:var(--body-color);background:var(--light-bg3);padding:8px 20px;border-radius:8px;display:inline-block">
                    <i class="ri-time-line" style="margin-right:4px"></i>
                    {{ __('translate.This invoice will be automatically deleted on') }} {{ $invoice->scheduled_deletion_at->format('F d, Y') }}.
                    <a href="{{ route('invoice.create') }}" style="color:var(--accent-color);font-weight:600">{{ __('translate.Create a new invoice') }}</a>
                </span>
            @else
                <span style="font-size:13px;color:#e67e22;background:#fff8e1;padding:8px 20px;border-radius:8px;display:inline-block">
                    <i class="ri-alert-line" style="margin-right:4px"></i>
                    {{ __('translate.Guest invoice — will be deleted in 24 hours.') }}
                    <a href="{{ route('user.register') }}" style="color:var(--accent-color);font-weight:600;text-decoration:underline">{{ __('translate.Sign up to save it for 3 months.') }}</a>
                </span>
            @endif
        </div>
        @endif

        @php $cur = $invoice->currency_icon; @endphp

        <div class="optech-invoice-doc" id="invoiceDoc">
            <div class="invoice-doc-inner">
                <div class="invoice-doc-header">
                    <div>
                        @if ($invoice->logo)
                            <img src="{{ $invoice->logo }}" alt="Logo" style="max-height:60px;margin-bottom:12px;display:block">
                        @endif
                        <span class="invoice-doc-label">{{ __('translate.INVOICE') }}</span>
                        <h1 class="invoice-doc-number">{{ $invoice->invoice_number }}</h1>
                    </div>
                    <div class="invoice-doc-meta">
                        <span class="invoice-doc-meta-label">{{ __('translate.Date Issued') }}</span>
                        <p class="invoice-doc-meta-value">{{ $invoice->created_at->format('F d, Y') }}</p>
                    </div>
                </div>

                <div class="invoice-doc-parties">
                    <div class="invoice-doc-party">
                        <h5 class="invoice-doc-party-label">{{ __('translate.FROM') }}</h5>
                        <h3 class="invoice-doc-party-name">{{ $invoice->business_name ?? $general_setting->app_name ?? __('translate.Your Business') }}</h3>
                        @if ($invoice->business_email)
                            <p class="invoice-doc-party-text">{{ $invoice->business_email }}</p>
                        @endif
                        @if ($invoice->business_address)
                            <p class="invoice-doc-party-text">{{ $invoice->business_address }}</p>
                        @endif
                    </div>
                    <div class="invoice-doc-party invoice-doc-party-right">
                        <h5 class="invoice-doc-party-label">{{ __('translate.TO') }}</h5>
                        <h3 class="invoice-doc-party-name">{{ $invoice->client_name }}</h3>
                        @if ($invoice->client_email)
                            <p class="invoice-doc-party-text">{{ $invoice->client_email }}</p>
                        @endif
                        @if ($invoice->client_address)
                            <p class="invoice-doc-party-text">{{ $invoice->client_address }}</p>
                        @endif
                    </div>
                </div>

                <table class="invoice-doc-table">
                    <thead>
                        <tr>
                            <th style="width:50%">{{ __('translate.Description') }}</th>
                            <th style="width:15%" class="text-end">{{ __('translate.Qty') }}</th>
                            <th style="width:17%" class="text-end">{{ __('translate.Unit Price') }}</th>
                            <th style="width:18%" class="text-end">{{ __('translate.Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->items as $item)
                        <tr>
                            <td>{{ $item['description'] }}</td>
                            <td class="text-end">{{ $item['quantity'] }}</td>
                            <td class="text-end">{{ $cur }} {{ number_format($item['unit_price'], 2) }}</td>
                            <td class="text-end">{{ $cur }} {{ number_format($item['total'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="invoice-doc-summary">
                    <div class="invoice-doc-summary-row">
                        <span>{{ __('translate.Subtotal') }}</span>
                        <span>{{ $cur }} {{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    @if ($invoice->tax_percentage > 0)
                    <div class="invoice-doc-summary-row">
                        <span>{{ __('translate.Tax') }} ({{ $invoice->tax_percentage }}%)</span>
                        <span>{{ $cur }} {{ number_format($invoice->tax_amount, 2) }}</span>
                    </div>
                    @endif
                    @if ($invoice->discount_amount > 0)
                    <div class="invoice-doc-summary-row">
                        <span>{{ __('translate.Discount') }}</span>
                        <span>-{{ $cur }} {{ number_format($invoice->discount_amount, 2) }}</span>
                    </div>
                    @endif
                    <div class="invoice-doc-summary-row invoice-doc-summary-total">
                        <span>{{ __('translate.Total Due') }}</span>
                        <span>{{ $cur }} {{ number_format($invoice->total, 2) }}</span>
                    </div>
                </div>

                @if ($invoice->note)
                <div class="invoice-doc-note">
                    <strong>{{ __('translate.Note') }}</strong>
                    <p>{{ $invoice->note }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.optech-invoice-print-btn,
.optech-invoice-new-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    margin: 0 6px;
    border: none;
}
.optech-invoice-print-btn {
    background: var(--accent-color);
    color: var(--white-color);
}
.optech-invoice-print-btn:hover {
    background: #1a3dcc;
    color: var(--white-color);
}
.optech-invoice-new-btn {
    background: var(--white-color);
    color: var(--accent-color);
    border: 1px solid var(--accent-color);
}
.optech-invoice-new-btn:hover {
    background: var(--light-bg3);
}

.optech-invoice-doc {
    max-width: 800px;
    margin: 0 auto 60px;
    background: var(--white-color);
    border-radius: 12px;
    box-shadow: 0 2px 20px rgba(10, 22, 94, 0.08);
    padding: 45px 50px;
}
.invoice-doc-inner {
    font-family: 'Sora', sans-serif;
}
.invoice-doc-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 35px;
    padding-bottom: 20px;
    border-bottom: 2px solid var(--light-color1);
}
.invoice-doc-label {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #999;
}
.invoice-doc-number {
    font-size: 28px;
    font-weight: 800;
    color: var(--heading-color);
    margin: 4px 0 0;
}
.invoice-doc-meta-label {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: #999;
    display: block;
    text-align: right;
}
.invoice-doc-meta-value {
    font-size: 15px;
    color: var(--heading-color);
    font-weight: 600;
    margin: 4px 0 0;
    text-align: right;
}
.invoice-doc-parties {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;
    gap: 30px;
}
.invoice-doc-party-label {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: #999;
    margin: 0 0 8px;
}
.invoice-doc-party-name {
    font-size: 16px;
    font-weight: 700;
    color: var(--heading-color);
    margin: 0 0 4px;
}
.invoice-doc-party-text {
    margin: 2px 0;
    color: var(--body-color);
    font-size: 14px;
    line-height: 1.5;
}
.invoice-doc-party-right {
    text-align: right;
}
.invoice-doc-party-right .invoice-doc-party-text {
    text-align: right;
}
.invoice-doc-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}
.invoice-doc-table thead th {
    background: var(--light-bg3);
    color: var(--heading-color);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 14px;
    text-align: left;
    border-bottom: 2px solid var(--light-color1);
}
.invoice-doc-table tbody td {
    padding: 12px 14px;
    border-bottom: 1px solid var(--light-color1);
    font-size: 14px;
    color: var(--body-color);
    vertical-align: top;
}
.invoice-doc-table tbody tr:last-child td {
    border-bottom: none;
}
.invoice-doc-summary {
    margin-left: auto;
    width: 300px;
    max-width: 100%;
    border-top: 2px solid var(--light-color1);
    padding-top: 16px;
}
.invoice-doc-summary-row {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    font-size: 14px;
    color: var(--body-color);
}
.invoice-doc-summary-total {
    font-size: 18px;
    font-weight: 800;
    color: var(--heading-color);
    border-top: 2px solid var(--heading-color);
    padding-top: 10px;
    margin-top: 6px;
}
.invoice-doc-note {
    margin-top: 24px;
    padding: 14px 18px;
    background: var(--light-bg3);
    border-radius: 8px;
    font-size: 13px;
    color: var(--body-color);
}
.invoice-doc-note strong {
    color: var(--heading-color);
    display: block;
    margin-bottom: 4px;
}
@media print {
    .optech-header-section, .optech-breadcrumb, .optech-invoice-print-btn, .optech-invoice-new-btn,
    .optech-footer-section, .optech-preloader-wrap, .paginacontainer, .search-overlay, #downloadPdfBtn {
        display: none !important;
    }
    body { background: #fff !important; margin: 0 !important; padding: 0 !important; }
    .section { padding: 0 !important; }
    .optech-invoice-doc {
        box-shadow: none !important; border-radius: 0 !important;
        padding: 20px !important; max-width: 100% !important; margin: 0 !important;
    }
    .invoice-doc-header { border-bottom-color: #000 !important; }
    .invoice-doc-table thead th {
        background: #f0f0f0 !important;
        -webkit-print-color-adjust: exact; print-color-adjust: exact;
    }
    .invoice-doc-summary-total { border-top-color: #000 !important; }
    .invoice-doc-note { background: #f5f5f5 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
@media (max-width: 767px) {
    .optech-invoice-doc { padding: 24px 20px; }
    .invoice-doc-number { font-size: 22px; }
    .invoice-doc-parties { flex-direction: column; }
    .invoice-doc-party-right { text-align: left; }
    .invoice-doc-party-right .invoice-doc-party-text { text-align: left; }
    .invoice-doc-summary { width: 100%; }
}
</style>
@endsection

@push('js_section')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
"use strict";
document.getElementById('downloadPdfBtn').addEventListener('click', function() {
    const el = document.getElementById('invoiceDoc');
    const btn = this;
    
    // Cache original styles to reset later
    const orig = { 
        w: el.style.width, 
        mw: el.style.maxWidth, 
        p: el.style.padding, 
        br: el.style.borderRadius, 
        bs: el.style.boxShadow 
    };

    // Update button visual state
    btn.disabled = true;
    btn.innerHTML = '<i class="ri-loader-4-line"></i> Generating...';

    /* FIX FOR RIGHT EDGE CLIPPING:
       Bringing the canvas snapshot width down to 720px and using a solid 40px internal 
       padding balances the layout perfectly inside html2canvas's rendering box.
    */
    el.style.width = '720px';
    el.style.maxWidth = '720px';
    el.style.padding = '40px'; 
    el.style.borderRadius = '0';
    el.style.boxShadow = 'none';

    void el.offsetHeight; // Force DOM layout reflow

    // Preload image logic to prevent empty assets in PDF render
    const images = Array.from(el.querySelectorAll('img'));
    const waitForImages = Promise.all(images.map(img => {
        if (img.complete && img.naturalWidth !== 0) return Promise.resolve();
        return new Promise(resolve => {
            img.addEventListener('load', resolve, { once: true });
            img.addEventListener('error', resolve, { once: true });
        });
    }));

    function restore() {
        el.style.width = orig.w;
        el.style.maxWidth = orig.mw;
        el.style.padding = orig.p;
        el.style.borderRadius = orig.br;
        el.style.boxShadow = orig.bs;
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-download-2-line"></i> Download PDF';
    }

    waitForImages
      .then(() => new Promise(r => requestAnimationFrame(() => requestAnimationFrame(r))))
      .then(() => {
        const opt = {
            margin:       0, // Zero out jsPDF margins so the CSS element padding handles it safely
            filename:     '{{ $invoice->invoice_number }}.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  {
                scale: 2,   // High-resolution text rendering
                useCORS: true,
                allowTaint: true,
                letterRendering: true,
                backgroundColor: '#ffffff',
                scrollX: 0,
                scrollY: 0  // Avoid offsets caused by current scroll position
            },
            jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' },
            pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
        };
        
        return html2pdf().set(opt).from(el).save();
    })
    .then(restore)
    .catch(function(err) {
        console.error('PDF generation failed:', err);
        restore();
    });
});
</script>
@endpush
