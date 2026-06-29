@extends('admin.master_layout')
@section('title')
    <title>{{ __('translate.Invoice') }} - {{ $invoice->invoice_number }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Invoice') }} #{{ $invoice->invoice_number }}</h3>
    <p class="crancy-header__text">{{ __('translate.Business Tools') }} >> {{ __('translate.Invoice') }}</p>
@endsection

@section('body-content')
<style>
.invoice-wrapper {
    max-width: 900px;
    margin: 0 auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.08);
    padding: 50px 55px;
}
.invoice-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 40px;
    padding-bottom: 25px;
    border-bottom: 2px solid #f0f0f0;
}
.invoice-title h1 {
    font-size: 32px;
    font-weight: 800;
    color: #1a1a2e;
    margin: 0;
    letter-spacing: 1px;
}
.invoice-title span {
    font-size: 13px;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 2px;
}
.invoice-meta {
    text-align: right;
}
.invoice-meta h2 {
    font-size: 18px;
    color: #1a1a2e;
    font-weight: 700;
    margin: 0 0 4px;
}
.invoice-meta p {
    margin: 2px 0;
    color: #666;
    font-size: 14px;
}
.parties {
    display: flex;
    justify-content: space-between;
    margin-bottom: 35px;
    gap: 30px;
}
.party-box {
    flex: 1;
}
.party-box h4 {
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: #888;
    margin: 0 0 10px;
}
.party-box h3 {
    font-size: 17px;
    font-weight: 700;
    color: #1a1a2e;
    margin: 0 0 5px;
}
.party-box p {
    margin: 2px 0;
    color: #555;
    font-size: 14px;
    line-height: 1.5;
}
.invoice-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 25px;
}
.invoice-table thead th {
    background: #f8f9fc;
    color: #1a1a2e;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 14px 16px;
    text-align: left;
    border-bottom: 2px solid #e9ecef;
}
.invoice-table tbody td {
    padding: 14px 16px;
    border-bottom: 1px solid #f0f0f0;
    font-size: 14px;
    color: #333;
    vertical-align: top;
}
.invoice-table tbody tr:last-child td {
    border-bottom: none;
}
.invoice-table .col-amount {
    text-align: right;
    font-weight: 600;
}
.summary {
    margin-left: auto;
    width: 340px;
    border-top: 2px solid #f0f0f0;
    padding-top: 18px;
    margin-top: 5px;
}
.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    font-size: 14px;
    color: #555;
}
.summary-row.total {
    font-size: 20px;
    font-weight: 800;
    color: #1a1a2e;
    border-top: 2px solid #1a1a2e;
    padding-top: 12px;
    margin-top: 8px;
}
.invoice-note {
    margin-top: 30px;
    padding: 16px 20px;
    background: #f8f9fc;
    border-radius: 8px;
    font-size: 13px;
    color: #666;
}
.invoice-note strong {
    color: #333;
    display: block;
    margin-bottom: 4px;
}
.action-bar {
    text-align: center;
    margin-bottom: 30px;
    display: flex;
    justify-content: center;
    gap: 12px;
}
.action-bar .crancy-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
@media print {
    .crancy-header, .crancy-smenu, .action-bar, footer, .crancy-body-area {
        display: none !important;
    }
    body {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .invoice-wrapper {
        box-shadow: none !important;
        border-radius: 0 !important;
        padding: 30px !important;
        max-width: 100% !important;
    }
    .invoice-header {
        border-bottom-color: #000 !important;
    }
    .invoice-table thead th {
        background: #f0f0f0 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .summary-row.total {
        border-top-color: #000 !important;
    }
    .invoice-wrapper {
        page-break-inside: avoid;
    }
}
</style>

<div class="action-bar">
    <button class="crancy-btn" onclick="window.print()">
        <i class="fas fa-print"></i> {{ __('translate.Print / Save PDF') }}
    </button>
    <a href="{{ route('admin.invoice.index') }}" class="crancy-btn" style="background:#6c757d">
        <i class="fas fa-arrow-left"></i> {{ __('translate.Back to List') }}
    </a>
</div>

<div class="invoice-wrapper">
    <div class="invoice-header">
        <div class="invoice-title">
            <span>{{ __('translate.INVOICE') }}</span>
            <h1>{{ $invoice->invoice_number }}</h1>
        </div>
        <div class="invoice-meta">
            <h2>{{ __('translate.Date Issued') }}</h2>
            <p>{{ $invoice->created_at->format('F d, Y') }}</p>
        </div>
    </div>

    <div class="parties">
        <div class="party-box">
            <h4>{{ __('translate.FROM') }}</h4>
            <h3>{{ $invoice->business_name ?? $general_setting->app_name ?? __('translate.Your Business') }}</h3>
            @if ($invoice->business_email)
                <p>{{ $invoice->business_email }}</p>
            @endif
            @if ($invoice->business_address)
                <p>{{ $invoice->business_address }}</p>
            @endif
        </div>
        <div class="party-box" style="text-align:right">
            <h4>{{ __('translate.TO') }}</h4>
            <h3>{{ $invoice->client_name }}</h3>
            @if ($invoice->client_email)
                <p>{{ $invoice->client_email }}</p>
            @endif
            @if ($invoice->client_address)
                <p>{{ $invoice->client_address }}</p>
            @endif
        </div>
    </div>

    <table class="invoice-table">
        <thead>
            <tr>
                <th style="width:50%">{{ __('translate.Description') }}</th>
                <th style="width:15%" class="col-amount">{{ __('translate.Quantity') }}</th>
                <th style="width:17%" class="col-amount">{{ __('translate.Unit Price') }}</th>
                <th style="width:18%" class="col-amount">{{ __('translate.Amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
            <tr>
                <td>{{ $item['description'] }}</td>
                <td class="col-amount">{{ $item['quantity'] }}</td>
                <td class="col-amount">{{ $general_setting->currency_icon ?? '$' }} {{ number_format($item['unit_price'], 2) }}</td>
                <td class="col-amount">{{ $general_setting->currency_icon ?? '$' }} {{ number_format($item['total'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-row">
            <span>{{ __('translate.Subtotal') }}</span>
            <span>{{ $general_setting->currency_icon ?? '$' }} {{ number_format($invoice->subtotal, 2) }}</span>
        </div>
        @if ($invoice->tax_percentage > 0)
        <div class="summary-row">
            <span>{{ __('translate.Tax') }} ({{ $invoice->tax_percentage }}%)</span>
            <span>{{ $general_setting->currency_icon ?? '$' }} {{ number_format($invoice->tax_amount, 2) }}</span>
        </div>
        @endif
        @if ($invoice->discount_amount > 0)
        <div class="summary-row">
            <span>{{ __('translate.Discount') }}</span>
            <span>-{{ $general_setting->currency_icon ?? '$' }} {{ number_format($invoice->discount_amount, 2) }}</span>
        </div>
        @endif
        <div class="summary-row total">
            <span>{{ __('translate.Total Due') }}</span>
            <span>{{ $general_setting->currency_icon ?? '$' }} {{ number_format($invoice->total, 2) }}</span>
        </div>
    </div>

    @if ($invoice->note)
    <div class="invoice-note">
        <strong>{{ __('translate.Note') }}</strong>
        {{ $invoice->note }}
    </div>
    @endif
</div>
@endsection
