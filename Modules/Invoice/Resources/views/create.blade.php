@extends('admin.master_layout')
@section('title')
    <title>{{ isset($invoice) ? __('translate.Edit Invoice') : __('translate.Create Invoice') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ isset($invoice) ? __('translate.Edit Invoice') : __('translate.Create Invoice') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Business Tools') }} >> {{ isset($invoice) ? __('translate.Edit Invoice') : __('translate.Create Invoice') }}</p>
@endsection

@section('body-content')
<section class="crancy-adashboard crancy-show">
    <div class="container container__bscreen">
        <div class="row">
            <div class="col-12">
                <div class="crancy-body">
                    <div class="crancy-dsinner">
                        <form action="{{ isset($invoice) ? route('admin.invoice.update', $invoice->id) : route('invoice.store') }}" method="POST" id="invoiceForm">
                            @csrf
                            @if (isset($invoice))
                                @method('PUT')
                            @endif

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="crancy__item-box mt-3">
                                        <h4 class="crancy-product-card__title mb-3">{{ __('translate.Business Information') }}</h4>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="crancy__item-label">{{ __('translate.Business Name') }}</label>
                                                <input class="crancy__item-input" type="text" name="business_name" value="{{ old('business_name', $invoice->business_name ?? '') }}" placeholder="{{ __('translate.Your company name') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="crancy__item-label">{{ __('translate.Business Email') }}</label>
                                                <input class="crancy__item-input" type="email" name="business_email" value="{{ old('business_email', $invoice->business_email ?? '') }}" placeholder="company@example.com">
                                            </div>
                                            <div class="col-12 mt-3">
                                                <label class="crancy__item-label">{{ __('translate.Business Address') }}</label>
                                                <textarea class="crancy__item-input crancy__item-textarea" name="business_address" rows="2" placeholder="{{ __('translate.Street, city, zip code') }}">{{ old('business_address', $invoice->business_address ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="crancy__item-box mt-3">
                                        <h4 class="crancy-product-card__title mb-3">{{ __('translate.Client Details') }}</h4>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="crancy__item-label">{{ __('translate.Client Name') }} *</label>
                                                <input class="crancy__item-input" type="text" name="client_name" value="{{ old('client_name', $invoice->client_name ?? '') }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="crancy__item-label">{{ __('translate.Client Email') }}</label>
                                                <input class="crancy__item-input" type="email" name="client_email" value="{{ old('client_email', $invoice->client_email ?? '') }}" placeholder="client@example.com">
                                            </div>
                                            <div class="col-12 mt-3">
                                                <label class="crancy__item-label">{{ __('translate.Client Address') }}</label>
                                                <textarea class="crancy__item-input crancy__item-textarea" name="client_address" rows="2" placeholder="{{ __('translate.Street, city, zip code') }}">{{ old('client_address', $invoice->client_address ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="crancy__item-box mt-4">
                                <div class="d-flex items-center justify-between flex-wrap" style="gap:10px">
                                    <h4 class="crancy-product-card__title mb-0">{{ __('translate.Line Items') }}</h4>
                                    <button type="button" class="crancy-btn" id="addItemBtn">
                                        <span>
                                            <svg width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect x="3" width="2" height="8" fill="white"/>
                                                <rect y="3" width="8" height="2" fill="white"/>
                                            </svg>
                                        </span>
                                        {{ __('translate.Add Item') }}
                                    </button>
                                </div>

                                <div class="table-responsive mt-3">
                                    <table class="table table-bordered" id="itemsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:5%">#</th>
                                                <th style="width:45%">{{ __('translate.Description') }}</th>
                                                <th style="width:15%">{{ __('translate.Quantity') }}</th>
                                                <th style="width:15%">{{ __('translate.Unit Price') }}</th>
                                                <th style="width:15%">{{ __('translate.Total') }}</th>
                                                <th style="width:5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsContainer">
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" class="text-end"><strong>{{ __('translate.Subtotal') }}</strong></td>
                                                <td><strong id="subtotalDisplay">0.00</strong></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-end">{{ __('translate.Tax') }} (%)</td>
                                                <td>
                                                    <input type="number" name="tax_percentage" id="taxPercentage" class="form-control form-control-sm" style="width:100px;display:inline" step="0.01" min="0" max="100" value="{{ old('tax_percentage', $invoice->tax_percentage ?? 0) }}">
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-end">{{ __('translate.Tax Amount') }}</td>
                                                <td><strong id="taxDisplay">0.00</strong></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-end">{{ __('translate.Discount') }} ({{ $general_setting->currency_icon ?? '$' }})</td>
                                                <td>
                                                    <input type="number" name="discount_amount" id="discountAmount" class="form-control form-control-sm" style="width:100px;display:inline" step="0.01" min="0" value="{{ old('discount_amount', $invoice->discount_amount ?? 0) }}">
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr class="table-active">
                                                <td colspan="4" class="text-end"><strong>{{ __('translate.Grand Total') }}</strong></td>
                                                <td><strong id="grandTotalDisplay" class="fs-5">0.00</strong></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="crancy__item-box mt-4">
                                <label class="crancy__item-label">{{ __('translate.Note') }} ({{ __('translate.optional') }})</label>
                                <textarea class="crancy__item-input crancy__item-textarea" name="note" rows="3" placeholder="{{ __('translate.Additional notes or payment instructions') }}">{{ old('note', $invoice->note ?? '') }}</textarea>
                            </div>

                            <div class="mt-4 mb-4">
                                <button type="submit" class="crancy-btn">
                                    {{ isset($invoice) ? __('translate.Update Invoice') : __('translate.Generate Invoice') }}
                                </button>
                                <a href="{{ route('admin.invoice.index') }}" class="crancy-btn" style="background:#6c757d">{{ __('translate.Cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('js_section')
<script>
"use strict";

let itemIndex = 0;
let currency = "{{ $invoice->currency_icon ?? $general_setting->currency_icon ?? '$' }}";

const oldItems = @json(old('items', $invoice->items ?? []));

function addItem(data) {
    const description = data?.description || '';
    const quantity = data?.quantity || 1;
    const unitPrice = data?.unit_price || 0;
    const total = (quantity * unitPrice).toFixed(2);
    const idx = itemIndex++;

    const html = `
        <tr class="item-row" data-index="${idx}">
            <td class="text-center item-number">${idx + 1}</td>
            <td>
                <input type="text" name="items[${idx}][description]" class="form-control form-control-sm item-desc" value="${description.replace(/"/g, '&quot;')}" placeholder="Item description" required>
            </td>
            <td>
                <input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm item-qty" value="${quantity}" step="0.01" min="0.01" required>
            </td>
            <td>
                <input type="number" name="items[${idx}][unit_price]" class="form-control form-control-sm item-price" value="${unitPrice}" step="0.01" min="0" required>
            </td>
            <td class="text-end item-row-total">${currency} ${total}</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-item"><i class="fas fa-times"></i></button>
            </td>
        </tr>
    `;

    $('#itemsContainer').append(html);
    recalc();
}

function recalc() {
    let subtotal = 0;

    $('.item-row').each(function() {
        const qty = parseFloat($(this).find('.item-qty').val()) || 0;
        const price = parseFloat($(this).find('.item-price').val()) || 0;
        const rowTotal = qty * price;
        subtotal += rowTotal;
        $(this).find('.item-row-total').text(currency + ' ' + rowTotal.toFixed(2));
    });

    const taxPct = parseFloat($('#taxPercentage').val()) || 0;
    const discount = parseFloat($('#discountAmount').val()) || 0;
    const taxAmt = subtotal * (taxPct / 100);
    const grandTotal = subtotal + taxAmt - discount;

    $('#subtotalDisplay').text(currency + ' ' + subtotal.toFixed(2));
    $('#taxDisplay').text(currency + ' ' + taxAmt.toFixed(2));
    $('#grandTotalDisplay').text(currency + ' ' + grandTotal.toFixed(2));
}

$('#addItemBtn').on('click', function() {
    addItem();
});

$(document).on('input', '.item-qty, .item-price, #taxPercentage, #discountAmount', function() {
    recalc();
});

$(document).on('click', '.remove-item', function() {
    $(this).closest('tr').remove();
    renumberRows();
    recalc();
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

// Load existing items
if (oldItems && oldItems.length > 0) {
    oldItems.forEach(function(item) {
        addItem(item);
    });
} else {
    addItem();
}
</script>
@endpush
