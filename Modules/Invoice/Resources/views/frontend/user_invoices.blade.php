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
                <li aria-current="page">{{ __($pageTitle) }}</li>
            </ul>
        </nav>
    </div>
</div>

<div class="section optech-section-padding2">
    <div class="container">
        <div class="optech-invoice-card">
            <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:12px;margin-bottom:24px">
                <h4 class="optech-invoice-card-title mb-0" style="border:none;padding:0;margin:0">{{ __('translate.My Invoices') }}</h4>
                <a href="{{ route('invoice.create') }}" class="optech-invoice-submit-btn" style="padding:10px 24px;font-size:14px;text-decoration:none">
                    <i class="ri-add-circle-line"></i> {{ __('translate.Create New Invoice') }}
                </a>
            </div>

            @if ($invoices->count() > 0)
                <div class="table-responsive">
                    <table class="optech-invoice-items-table" style="font-size:14px">
                        <thead>
                            <tr>
                                <th>{{ __('translate.Invoice No') }}</th>
                                <th>{{ __('translate.Client') }}</th>
                                <th>{{ __('translate.Amount') }}</th>
                                <th>{{ __('translate.Date') }}</th>
                                <th>{{ __('translate.Expires') }}</th>
                                <th>{{ __('translate.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $invoice)
                            <tr>
                                <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                <td>{{ $invoice->client_name }}</td>
                                <td>{{ $invoice->currency_icon }} {{ number_format($invoice->total, 2) }}</td>
                                <td>{{ $invoice->created_at->format('d M Y') }}</td>
                                <td>
                                    @if ($invoice->scheduled_deletion_at)
                                        <span style="font-size:12px;color:var(--body-color)">
                                            {{ $invoice->scheduled_deletion_at->diffForHumans() }}
                                        </span>
                                    @else
                                        <span style="font-size:12px;color:#999">—</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('invoice.show', $invoice->id) }}" class="optech-invoice-btn" style="padding:6px 14px;font-size:12px;text-decoration:none">
                                        <i class="ri-eye-line"></i> {{ __('translate.View') }}
                                    </a>
                                    <a href="{{ route('invoice.edit', $invoice->id) }}" class="optech-invoice-btn" style="padding:6px 14px;font-size:12px;text-decoration:none">
                                        <i class="ri-edit-line"></i> {{ __('translate.Edit') }}
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $invoices->links() }}
                </div>
            @else
                <div style="text-align:center;padding:60px 20px;color:var(--body-color)">
                    <i class="ri-file-list-3-line" style="font-size:48px;color:var(--light-color2);display:block;margin-bottom:16px"></i>
                    <p style="font-size:16px;margin-bottom:20px">{{ __('translate.You have not created any invoices yet') }}</p>
                    <a href="{{ route('invoice.create') }}" class="optech-invoice-submit-btn" style="padding:12px 28px;text-decoration:none;display:inline-flex">
                        <i class="ri-add-circle-line"></i> {{ __('translate.Create Your First Invoice') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.optech-invoice-items-table tbody td {
    padding: 12px 14px;
}
.optech-invoice-items-table tbody tr:hover {
    background: var(--light-bg3);
}
.pagination {
    display: flex;
    gap: 6px;
    justify-content: center;
    list-style: none;
    padding: 0;
}
.pagination .page-item .page-link {
    padding: 8px 14px;
    border: 1px solid var(--light-color2);
    border-radius: 6px;
    color: var(--heading-color);
    text-decoration: none;
    font-size: 14px;
}
.pagination .page-item.active .page-link {
    background: var(--accent-color);
    color: var(--white-color);
    border-color: var(--accent-color);
}
</style>
@endsection
