@extends('user.dashboard_layout')
@section('title')
    <title>{{ __('translate.My Downloads') }}</title>
@endsection
@section('breadcrumb')
    <h1 class="post__title">{{ __('translate.My Downloads') }}</h1>
    <nav class="breadcrumbs">
        <ul>
            <li><a href="{{ route('user.dashboard') }}">{{ __('translate.Home') }}</a></li>
            <li>{{ __('translate.My Downloads') }}</li>
        </ul>
    </nav>
@endsection
@section('dashboard-content')
    <div class="dashbord_table_top">
        <div class="dashbord_table_top_left">
            <div class="dashbord_table_top_left_text">
                <p>{{ __('translate.My Purchased Digital Products') }}</p>
            </div>
        </div>
    </div>

    @if($orders->isEmpty())
        <div class="m-5 text-center">
            <h4>{{ __('translate.No digital purchases yet') }}</h4>
            <a href="{{ route('product.shop') }}" class="optech-default-btn optech-border-btn mt-3">
                <span>{{ __('translate.Browse Shop') }}</span>
            </a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('translate.Order') }}</th>
                        <th>{{ __('translate.Product') }}</th>
                        <th>{{ __('translate.License Key') }}</th>
                        <th>{{ __('translate.Download') }}</th>
                        <th>{{ __('translate.Support Until') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        @foreach($order->order_detail as $detail)
                            <tr>
                                <td>{{ $order->order_id }}</td>
                                <td>{{ $detail->singleProduct?->front_translate?->name ?? $detail->singleProduct?->translate?->name ?? __('translate.N/A') }}</td>
                                <td>
                                    @if($detail->license)
                                        <code class="text-primary">{{ $detail->license->license_key }}</code>
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                                <td>
                                    @if($detail->singleProduct && $detail->singleProduct->currentFile)
                                        <a href="{{ route('user.downloads.file', $detail->id) }}" class="optech-default-btn optech-border-btn">
                                            <span>{{ __('translate.Download') }}</span>
                                        </a>
                                    @else
                                        <span class="text-muted">{{ __('translate.No file') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php $p = $detail->singleProduct; @endphp
                                    @if($p && $p->update_support_months)
                                        {{ \Carbon\Carbon::parse($order->created_at)->addMonths($p->update_support_months)->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="optech-navigation">
                <nav class="navigation pagination" aria-label="{{ __('translate.Pagination') }}">
                    <div class="nav-links">
                        <a class="next page-numbers" href="{{ $orders->appends(request()->query())->previousPageUrl() }}">
                            <i class="ri-arrow-left-s-line"></i>
                        </a>
                        @for ($i = 1; $i <= $orders->lastPage(); $i++)
                            @if ($i == $orders->currentPage())
                                <span aria-current="page" class="page-numbers current">{{ $i }}</span>
                            @else
                                <a class="page-numbers" href="{{ $orders->appends(request()->query())->url($i) }}">{{ $i }}</a>
                            @endif
                        @endfor
                        <a class="next page-numbers" href="{{ $orders->appends(request()->query())->nextPageUrl() }}">
                            <i class="ri-arrow-right-s-line"></i>
                        </a>
                    </div>
                </nav>
            </div>
        @endif
    @endif
@endsection
