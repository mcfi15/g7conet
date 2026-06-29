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
                <li aria-current="page">{{ __($pageTitle) }}</li>
            </ul>
        </nav>
    </div>
</div>

<div class="section optech-section-padding2">
    <div class="container">
        <div class="optech-invoice-card">
            <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:12px;margin-bottom:24px">
                <h4 class="optech-invoice-card-title mb-0" style="border:none;padding:0;margin:0">{{ __('translate.My QR Codes') }}</h4>
                <a href="{{ route('qrcode.create') }}" class="optech-invoice-submit-btn" style="padding:10px 24px;font-size:14px;text-decoration:none">
                    <i class="ri-add-circle-line"></i> {{ __('translate.Create New QR Code') }}
                </a>
            </div>

            @if ($qrCodes->count() > 0)
                <div class="table-responsive">
                    <table class="optech-invoice-items-table" style="font-size:14px">
                        <thead>
                            <tr>
                                <th>{{ __('translate.ID') }}</th>
                                <th>{{ __('translate.Content') }}</th>
                                <th>{{ __('translate.Size') }}</th>
                                <th>{{ __('translate.Date') }}</th>
                                <th>{{ __('translate.Expires') }}</th>
                                <th>{{ __('translate.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($qrCodes as $qrCode)
                            <tr>
                                <td>#{{ $qrCode->id }}</td>
                                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $qrCode->content }}</td>
                                <td>{{ $qrCode->size }}x{{ $qrCode->size }}</td>
                                <td>{{ $qrCode->created_at->format('d M Y') }}</td>
                                <td>
                                    @if ($qrCode->scheduled_deletion_at)
                                        <span style="font-size:12px;color:var(--body-color)">
                                            {{ $qrCode->scheduled_deletion_at->diffForHumans() }}
                                        </span>
                                    @else
                                        <span style="font-size:12px;color:#999">—</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('qrcode.show', $qrCode->id) }}" class="optech-invoice-btn" style="padding:6px 14px;font-size:12px;text-decoration:none">
                                        <i class="ri-eye-line"></i> {{ __('translate.View') }}
                                    </a>
                                    <a href="{{ route('qrcode.edit', $qrCode->id) }}" class="optech-invoice-btn" style="padding:6px 14px;font-size:12px;text-decoration:none">
                                        <i class="ri-edit-line"></i> {{ __('translate.Edit') }}
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $qrCodes->links() }}
                </div>
            @else
                <div style="text-align:center;padding:60px 20px;color:var(--body-color)">
                    <i class="ri-qr-code-line" style="font-size:48px;color:var(--light-color2);display:block;margin-bottom:16px"></i>
                    <p style="font-size:16px;margin-bottom:20px">{{ __('translate.You have not created any QR codes yet') }}</p>
                    <a href="{{ route('qrcode.create') }}" class="optech-invoice-submit-btn" style="padding:12px 28px;text-decoration:none;display:inline-flex">
                        <i class="ri-add-circle-line"></i> {{ __('translate.Create Your First QR Code') }}
                    </a>
                </div>
            @endif
        </div>
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
    padding: 12px 14px;
    border-bottom: 1px solid var(--light-color1);
}
.optech-invoice-items-table tbody tr:hover {
    background: var(--light-bg3);
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
