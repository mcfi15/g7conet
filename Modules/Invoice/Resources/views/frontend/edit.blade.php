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
                <li><a href="{{ route('invoice.create') }}">{{ __('translate.Invoice Generator') }}</a></li>
                <li><a href="{{ route('user.invoices') }}">{{ __('translate.My Invoices') }}</a></li>
                <li aria-current="page">#{{ $invoice->invoice_number }}</li>
            </ul>
        </nav>
    </div>
</div>

<div class="section optech-section-padding2">
    <div class="container">
        <form action="{{ route('invoice.update', $invoice->id) }}" method="POST" id="invoiceForm">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-7">
                    <div class="optech-invoice-card">
                        <h4 class="optech-invoice-card-title">{{ __('translate.Business Information') }}</h4>
                        <div class="row">
                            <div class="col-12">
                                <div class="optech-invoice-field">
                                    <label>{{ __('translate.Add Your Logo') }} ({{ __('translate.optional') }})</label>
                                    <input type="file" id="logoInput" accept="image/*" style="padding-top:8px !important;height:auto">
                                    <input type="hidden" name="logo" id="logoData" value="{{ $invoice->logo }}">
                                    <div id="logoPreview" class="mt-2" style="{{ $invoice->logo ? '' : 'display:none' }}">
                                        <img src="{{ $invoice->logo }}" alt="Logo" style="max-height:60px;border:1px solid var(--light-color2);border-radius:6px;padding:4px">
                                        <button type="button" id="removeLogo" class="optech-invoice-remove-btn ms-2" style="vertical-align:top;margin-top:4px"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="optech-invoice-field">
                                    <label>{{ __('translate.Business Name') }}</label>
                                    <input type="text" name="business_name" value="{{ old('business_name', $invoice->business_name) }}" placeholder="{{ __('translate.Your company name') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="optech-invoice-field">
                                    <label>{{ __('translate.Business Email') }}</label>
                                    <input type="email" name="business_email" value="{{ old('business_email', $invoice->business_email) }}" placeholder="company@example.com">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="optech-invoice-field">
                                    <label>{{ __('translate.Business Address') }}</label>
                                    <textarea name="business_address" rows="2" placeholder="{{ __('translate.Street, city, zip code') }}">{{ old('business_address', $invoice->business_address) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="optech-invoice-card">
                        <h4 class="optech-invoice-card-title">{{ __('translate.Client Details') }}</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="optech-invoice-field">
                                    <label>{{ __('translate.Client Name') }} *</label>
                                    <input type="text" name="client_name" value="{{ old('client_name', $invoice->client_name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="optech-invoice-field">
                                    <label>{{ __('translate.Client Email') }}</label>
                                    <input type="email" name="client_email" value="{{ old('client_email', $invoice->client_email) }}" placeholder="client@example.com">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="optech-invoice-field">
                                    <label>{{ __('translate.Client Address') }}</label>
                                    <textarea name="client_address" rows="2" placeholder="{{ __('translate.Street, city, zip code') }}">{{ old('client_address', $invoice->client_address) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="optech-invoice-card">
                        <div class="row align-items-center">
                            <div class="col-sm-6">
                                <div class="optech-invoice-field mb-0">
                                    <label>{{ __('translate.Currency') }}</label>
                                    <select name="currency" id="currencySelect" style="width:100%;height:44px;padding:8px 14px;border:1px solid var(--light-color2);border-radius:8px;font-size:14px;color:var(--heading-color);background:var(--white-color);border-bottom:1px solid var(--light-color2) !important">
                                         @forelse($currencies as $code => $icon)
        <option value="{{ $code }}" {{ $code === $invoice->currency ? 'selected' : '' }}>{{ $code }} ({{ $icon }})</option>
    @empty
        <option value="USD" {{ $invoice->currency == 'USD' ? 'selected' : '' }}>USD ($)</option>
    @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="optech-invoice-card">
                        <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:10px;margin-bottom:20px">
                            <h4 class="optech-invoice-card-title mb-0">{{ __('translate.Line Items') }}</h4>
                            <button type="button" class="optech-invoice-btn" id="addItemBtn">
                                <i class="ri-add-line"></i> {{ __('translate.Add Item') }}
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="optech-invoice-items-table" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th style="width:5%">#</th>
                                        <th style="width:40%">{{ __('translate.Description') }}</th>
                                        <th style="width:15%">{{ __('translate.Qty') }}</th>
                                        <th style="width:18%">{{ __('translate.Unit Price') }}</th>
                                        <th style="width:17%">{{ __('translate.Total') }}</th>
                                        <th style="width:5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsContainer"></tbody>
                            </table>
                        </div>
                        <div class="optech-invoice-summary">
                            <div class="optech-invoice-summary-row">
                                <span>{{ __('translate.Subtotal') }}</span>
                                <span><strong id="subtotalDisplay">0.00</strong></span>
                            </div>
                            <div class="optech-invoice-summary-row">
                                <span>{{ __('translate.Tax') }} (%)</span>
                                <span>
                                    <input type="number" name="tax_percentage" id="taxPercentage" class="optech-invoice-summary-input" step="0.01" min="0" max="100" value="{{ $invoice->tax_percentage }}">
                                </span>
                            </div>
                            <div class="optech-invoice-summary-row">
                                <span>{{ __('translate.Tax Amount') }}</span>
                                <span><strong id="taxDisplay">0.00</strong></span>
                            </div>
                            <div class="optech-invoice-summary-row">
                                <span>{{ __('translate.Discount') }}</span>
                                <span>
                                    <input type="number" name="discount_amount" id="discountAmount" class="optech-invoice-summary-input" step="0.01" min="0" value="{{ $invoice->discount_amount }}">
                                </span>
                            </div>
                            <div class="optech-invoice-summary-row optech-invoice-summary-total">
                                <span>{{ __('translate.Grand Total') }}</span>
                                <span id="grandTotalDisplay">0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="optech-invoice-card">
                        <div class="optech-invoice-field">
                            <label>{{ __('translate.Note') }} ({{ __('translate.optional') }})</label>
                            <textarea name="note" rows="3" placeholder="{{ __('translate.Additional notes or payment instructions') }}">{{ old('note', $invoice->note) }}</textarea>
                        </div>
                    </div>

                    <div class="optech-invoice-actions">
                        <button type="submit" class="optech-invoice-submit-btn">
                            <i class="ri-save-line"></i> {{ __('translate.Save Changes') }}
                        </button>
                        <a href="{{ route('invoice.show', $invoice->id) }}" class="optech-invoice-btn" style="display:inline-flex;align-items:center;gap:6px;padding:14px 24px;text-decoration:none;font-weight:700;font-size:14px">
                            <i class="ri-close-line"></i> {{ __('translate.Cancel') }}
                        </a>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="optech-invoice-preview-wrap" id="previewWrap">
                        <div class="optech-invoice-preview-header">
                            <i class="ri-eye-line"></i> {{ __('translate.Live Preview') }}
                        </div>
                        <div class="optech-invoice-preview-body" id="previewBody">
                            <div class="preview-invoice">
                                <div class="preview-header">
                                    <div>
                                        <div class="preview-invoice-label">{{ __('translate.INVOICE') }}</div>
                                        <div class="preview-invoice-number" id="previewInvoiceNo">{{ $invoice->invoice_number }}</div>
                                    </div>
                                    <div class="preview-meta">
                                        <div class="preview-meta-label">{{ __('translate.Date Issued') }}</div>
                                        <div class="preview-meta-value">{{ $invoice->created_at->format('F d, Y') }}</div>
                                    </div>
                                </div>
                                <div class="preview-parties">
                                    <div class="preview-party">
                                        <div class="preview-party-label">{{ __('translate.FROM') }}</div>
                                        <div class="preview-party-name" id="previewBusinessName">{{ $invoice->business_name ?: '—' }}</div>
                                        <div class="preview-party-detail" id="previewBusinessEmail">{{ $invoice->business_email }}</div>
                                        <div class="preview-party-detail" id="previewBusinessAddress">{{ $invoice->business_address }}</div>
                                    </div>
                                    <div class="preview-party preview-party-right">
                                        <div class="preview-party-label">{{ __('translate.TO') }}</div>
                                        <div class="preview-party-name" id="previewClientName">{{ $invoice->client_name ?: '—' }}</div>
                                        <div class="preview-party-detail" id="previewClientEmail">{{ $invoice->client_email }}</div>
                                        <div class="preview-party-detail" id="previewClientAddress">{{ $invoice->client_address }}</div>
                                    </div>
                                </div>
                                <table class="preview-table">
                                    <thead>
                                        <tr>
                                            <th style="width:45%">{{ __('translate.Description') }}</th>
                                            <th style="width:18%">{{ __('translate.Qty') }}</th>
                                            <th style="width:20%">{{ __('translate.Price') }}</th>
                                            <th style="width:17%">{{ __('translate.Total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="previewItems">
                                        <tr>
                                            <td colspan="4" style="text-align:center;color:#999;padding:30px 0">
                                                {{ __('translate.Add items to see preview') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="preview-summary">
                                    <div class="preview-summary-row">
                                        <span>{{ __('translate.Subtotal') }}</span>
                                        <span id="previewSubtotal">0.00</span>
                                    </div>
                                    <div class="preview-summary-row" id="previewTaxRow" style="display:none">
                                        <span>{{ __('translate.Tax') }} (<span id="previewTaxPct">0</span>%)</span>
                                        <span id="previewTax">0.00</span>
                                    </div>
                                    <div class="preview-summary-row" id="previewDiscountRow" style="display:none">
                                        <span>{{ __('translate.Discount') }}</span>
                                        <span id="previewDiscount">0.00</span>
                                    </div>
                                    <div class="preview-summary-row preview-summary-total">
                                        <span>{{ __('translate.Total Due') }}</span>
                                        <span id="previewGrandTotal">0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.optech-invoice-card {
    background: var(--white-color);
    border-radius: 12px;
    padding: 28px 30px;
    margin-bottom: 24px;
    box-shadow: 0 2px 12px rgba(10, 22, 94, 0.06);
}
.optech-invoice-card-title {
    font-size: 17px;
    font-weight: 700;
    color: var(--heading-color);
    margin-bottom: 18px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--light-color1);
}
.optech-invoice-field {
    margin-bottom: 16px;
}
.optech-invoice-field label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--heading-color);
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.optech-invoice-field input,
.optech-invoice-field textarea {
    width: 100%;
    height: 44px;
    padding: 8px 14px;
    border: 1px solid var(--light-color2);
    border-radius: 8px;
    font-size: 14px;
    color: var(--heading-color);
    background: var(--white-color);
    transition: border-color 0.2s;
    border-bottom: 1px solid var(--light-color2) !important;
}
.optech-invoice-field input:focus,
.optech-invoice-field textarea:focus {
    border-color: var(--accent-color) !important;
    outline: none;
    box-shadow: 0 0 0 3px rgba(43, 77, 255, 0.08);
}
.optech-invoice-field textarea {
    height: 70px;
    resize: vertical;
    padding-top: 10px;
}
.optech-invoice-items-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}
.optech-invoice-items-table thead th {
    background: var(--light-bg3);
    color: var(--heading-color);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 10px 12px;
    text-align: left;
    border-bottom: 1px solid var(--light-color1);
}
.optech-invoice-items-table tbody td {
    padding: 8px 12px;
    border-bottom: 1px solid var(--light-color1);
    vertical-align: middle;
}
.optech-invoice-items-table input {
    width: 100%;
    height: 36px;
    padding: 4px 10px;
    border: 1px solid var(--light-color2);
    border-radius: 6px;
    font-size: 13px;
    color: var(--heading-color);
    border-bottom: 1px solid var(--light-color2) !important;
}
.optech-invoice-items-table input:focus {
    border-color: var(--accent-color) !important;
    outline: none;
    box-shadow: 0 0 0 2px rgba(43, 77, 255, 0.06);
}
.optech-invoice-items-table .item-row-total {
    font-weight: 600;
    color: var(--heading-color);
}
.optech-invoice-remove-btn {
    width: 30px;
    height: 30px;
    border: none;
    background: #fee2e2;
    color: #dc2626;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}
.optech-invoice-remove-btn:hover {
    background: #fecaca;
}
.optech-invoice-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 18px;
    background: var(--light-bg3);
    color: var(--accent-color);
    border: 1px solid var(--accent-color);
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}
.optech-invoice-btn:hover {
    background: var(--accent-color);
    color: var(--white-color);
}
.optech-invoice-summary {
    margin-top: 16px;
    margin-left: auto;
    width: 300px;
    max-width: 100%;
}
.optech-invoice-summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 7px 0;
    font-size: 14px;
    color: var(--body-color);
}
.optech-invoice-summary-row strong {
    color: var(--heading-color);
}
.optech-invoice-summary-input {
    width: 90px;
    height: 34px;
    padding: 4px 10px;
    border: 1px solid var(--light-color2);
    border-radius: 6px;
    font-size: 13px;
    text-align: right;
    color: var(--heading-color);
    border-bottom: 1px solid var(--light-color2) !important;
}
.optech-invoice-summary-input:focus {
    border-color: var(--accent-color) !important;
    outline: none;
    box-shadow: 0 0 0 2px rgba(43, 77, 255, 0.06);
}
.optech-invoice-summary-total {
    font-size: 18px;
    font-weight: 800;
    color: var(--heading-color);
    border-top: 2px solid var(--heading-color);
    padding-top: 10px;
    margin-top: 6px;
}
.optech-invoice-actions {
    display: flex;
    gap: 12px;
    margin-top: 8px;
    margin-bottom: 40px;
}
.optech-invoice-submit-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 32px;
    background: var(--accent-color);
    color: var(--white-color);
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s;
}
.optech-invoice-submit-btn:hover {
    background: #1a3dcc;
}

/* Live Preview */
.optech-invoice-preview-wrap {
    position: sticky;
    top: 100px;
    background: var(--white-color);
    border-radius: 12px;
    box-shadow: 0 2px 20px rgba(10, 22, 94, 0.08);
    overflow: hidden;
}
.optech-invoice-preview-header {
    padding: 14px 20px;
    background: var(--heading-color);
    color: var(--white-color);
    font-size: 14px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
}
.optech-invoice-preview-body {
    padding: 24px;
}
.preview-invoice {
    font-family: 'Sora', sans-serif;
}
.preview-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--light-color1);
}
.preview-invoice-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #999;
}
.preview-invoice-number {
    font-size: 20px;
    font-weight: 800;
    color: var(--heading-color);
    margin-top: 2px;
}
.preview-meta-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: #999;
}
.preview-meta-value {
    font-size: 13px;
    color: var(--heading-color);
    font-weight: 600;
    margin-top: 2px;
    text-align: right;
}
.preview-parties {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    gap: 20px;
}
.preview-party-label {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: #999;
    margin-bottom: 6px;
}
.preview-party-name {
    font-size: 14px;
    font-weight: 700;
    color: var(--heading-color);
    margin-bottom: 3px;
}
.preview-party-detail {
    font-size: 12px;
    color: var(--body-color);
    line-height: 1.4;
}
.preview-party-right {
    text-align: right;
}
.preview-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 16px;
    font-size: 13px;
}
.preview-table thead th {
    background: var(--light-bg3);
    color: var(--heading-color);
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 8px 10px;
    text-align: left;
    border-bottom: 1px solid var(--light-color1);
}
.preview-table tbody td {
    padding: 8px 10px;
    border-bottom: 1px solid var(--light-color1);
    color: var(--body-color);
}
.preview-summary {
    margin-left: auto;
    width: 200px;
    max-width: 100%;
}
.preview-summary-row {
    display: flex;
    justify-content: space-between;
    padding: 4px 0;
    font-size: 12px;
    color: var(--body-color);
}
.preview-summary-total {
    font-size: 16px;
    font-weight: 800;
    color: var(--heading-color);
    border-top: 2px solid var(--heading-color);
    padding-top: 8px;
    margin-top: 4px;
}
@media (max-width: 991px) {
    .optech-invoice-preview-wrap {
        position: static;
        margin-top: 24px;
        margin-bottom: 40px;
    }
}
@media (max-width: 767px) {
    .optech-invoice-card {
        padding: 20px;
    }
    .optech-invoice-summary {
        width: 100%;
    }
    .optech-invoice-preview-body {
        padding: 16px;
    }
}
</style>
@endsection

@push('js_section')
<script>
"use strict";

let itemIndex = 0;

function getCurrency() {
    const sel = document.getElementById('currencySelect');
    if (sel) {
        const text = sel.options[sel.selectedIndex].text;
        const match = text.match(/\(([^)]+)\)/);
        return match ? match[1] : '$';
    }
    return '$';
}

function addItem(data) {
    const description = data?.description || '';
    const quantity = data?.quantity || 1;
    const unitPrice = data?.unit_price || 0;
    const total = (quantity * unitPrice).toFixed(2);
    const idx = itemIndex++;
    const cur = getCurrency();

    const html = `
        <tr class="item-row" data-index="${idx}">
            <td class="text-center item-number" style="padding-top:14px;font-weight:600;color:var(--heading-color)">${idx + 1}</td>
            <td>
                <input type="text" name="items[${idx}][description]" class="item-desc" value="${description.replace(/"/g, '&quot;')}" placeholder="Item description" required>
            </td>
            <td>
                <input type="number" name="items[${idx}][quantity]" class="item-qty" value="${quantity}" step="0.01" min="0.01" required>
            </td>
            <td>
                <input type="number" name="items[${idx}][unit_price]" class="item-price" value="${unitPrice}" step="0.01" min="0" required>
            </td>
            <td class="item-row-total text-end">${cur} ${total}</td>
            <td class="text-center">
                <button type="button" class="optech-invoice-remove-btn remove-item"><i class="ri-close-line"></i></button>
            </td>
        </tr>
    `;

    $('#itemsContainer').append(html);
    recalc();
    updatePreview();
}

function recalc() {
    const cur = getCurrency();
    let subtotal = 0;
    $('.item-row').each(function() {
        const qty = parseFloat($(this).find('.item-qty').val()) || 0;
        const price = parseFloat($(this).find('.item-price').val()) || 0;
        const rowTotal = qty * price;
        subtotal += rowTotal;
        $(this).find('.item-row-total').text(cur + ' ' + rowTotal.toFixed(2));
    });

    const taxPct = parseFloat($('#taxPercentage').val()) || 0;
    const discount = parseFloat($('#discountAmount').val()) || 0;
    const taxAmt = subtotal * (taxPct / 100);
    const grandTotal = subtotal + taxAmt - discount;

    $('#subtotalDisplay').text(cur + ' ' + subtotal.toFixed(2));
    $('#taxDisplay').text(cur + ' ' + taxAmt.toFixed(2));
    $('#grandTotalDisplay').text(cur + ' ' + grandTotal.toFixed(2));
}

function updatePreview() {
    const cur = getCurrency();
    const bizName = $('input[name="business_name"]').val();
    const bizEmail = $('input[name="business_email"]').val();
    const bizAddr = $('textarea[name="business_address"]').val();
    const clientName = $('input[name="client_name"]').val();
    const clientEmail = $('input[name="client_email"]').val();
    const clientAddr = $('textarea[name="client_address"]').val();
    const logoData = $('#logoData').val();

    $('#previewBusinessName').text(bizName || '—');
    $('#previewBusinessEmail').text(bizEmail || '');
    $('#previewBusinessAddress').text(bizAddr || '');
    $('#previewClientName').text(clientName || '—');
    $('#previewClientEmail').text(clientEmail || '');
    $('#previewClientAddress').text(clientAddr || '');

    if (logoData) {
        if (!$('#previewLogoImg').length) {
            $('.preview-header > div:first').prepend('<img id="previewLogoImg" style="max-height:50px;margin-bottom:8px;display:block">');
        }
        $('#previewLogoImg').attr('src', logoData).show();
    } else {
        $('#previewLogoImg').remove();
    }

    const taxPct = parseFloat($('#taxPercentage').val()) || 0;
    const discount = parseFloat($('#discountAmount').val()) || 0;

    let previewRows = '';
    let subtotal = 0;

    $('.item-row').each(function() {
        const desc = $(this).find('.item-desc').val() || '—';
        const qty = parseFloat($(this).find('.item-qty').val()) || 0;
        const price = parseFloat($(this).find('.item-price').val()) || 0;
        const total = qty * price;
        subtotal += total;
        previewRows += `
            <tr>
                <td>${desc}</td>
                <td>${qty}</td>
                <td>${cur} ${price.toFixed(2)}</td>
                <td style="font-weight:600">${cur} ${total.toFixed(2)}</td>
            </tr>
        `;
    });

    if (previewRows === '') {
        previewRows = '<tr><td colspan="4" style="text-align:center;color:#999;padding:30px 0">{{ __('translate.Add items to see preview') }}</td></tr>';
    }

    $('#previewItems').html(previewRows);

    const taxAmt = subtotal * (taxPct / 100);
    const grandTotal = subtotal + taxAmt - discount;

    $('#previewSubtotal').text(cur + ' ' + subtotal.toFixed(2));

    if (taxPct > 0) {
        $('#previewTaxRow').show();
        $('#previewTaxPct').text(taxPct);
        $('#previewTax').text(cur + ' ' + taxAmt.toFixed(2));
    } else {
        $('#previewTaxRow').hide();
    }

    if (discount > 0) {
        $('#previewDiscountRow').show();
        $('#previewDiscount').text(cur + ' ' + discount.toFixed(2));
    } else {
        $('#previewDiscountRow').hide();
    }

    $('#previewGrandTotal').text(cur + ' ' + grandTotal.toFixed(2));
}

$('#addItemBtn').on('click', function() { addItem(); });

$(document).on('input', '.item-qty, .item-price, #taxPercentage, #discountAmount', function() {
    recalc();
    updatePreview();
});

$(document).on('input', 'input[name="business_name"], input[name="business_email"], textarea[name="business_address"], input[name="client_name"], input[name="client_email"], textarea[name="client_address"]', function() {
    updatePreview();
});

$(document).on('click', '.remove-item', function() {
    $(this).closest('tr').remove();
    renumberRows();
    recalc();
    updatePreview();
});

function renumberRows() {
    $('.item-row').each(function(i) {
        $(this).find('.item-number').text(i + 1);
    });
}

$('#invoiceForm').on('submit', function() {
    if ($('.item-row').length === 0) {
        alert('{{ __('translate.Please add at least one item') }}');
        return false;
    }
});

$('#currencySelect').on('change', function() {
    $('.item-row').each(function() {
        $(this).find('input[name$="[quantity]"], input[name$="[unit_price]"]').trigger('input');
    });
    recalc();
    updatePreview();
});

$('#logoInput').on('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#logoData').val(e.target.result);
            $('#logoPreview img').attr('src', e.target.result);
            $('#logoPreview').show();
            updatePreview();
        };
        reader.readAsDataURL(file);
    }
});

$('#removeLogo').on('click', function() {
    $('#logoInput').val('');
    $('#logoData').val('');
    $('#logoPreview').hide();
    updatePreview();
});

const oldItems = @json(old('items', $invoice->items ?? []));
if (oldItems && oldItems.length > 0) {
    oldItems.forEach(function(item) {
        addItem(item);
    });
} else {
    addItem();
}
</script>
@endpush
