@extends('admin.master_layout')
@section('title')
    <title>{{ __('translate.Invoices') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Invoices') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Business Tools') }} >> {{ __('translate.Invoices') }}</p>
@endsection

@section('body-content')
    <section class="crancy-adashboard crancy-show">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <div class="crancy-dsinner">
                            <div class="crancy-table crancy-table--v3 mg-top-30">
                                <div class="crancy-customer-filter">
                                    <div class="crancy-customer-filter__single crancy-customer-filter__single--csearch d-flex items-center justify-between create_new_btn_box flex-wrap" style="gap:10px">
                                        <div class="crancy-header__form crancy-header__form--customer create_new_btn_inline_box">
                                            <h4 class="crancy-product-card__title">{{ __('translate.All Invoices') }}</h4>
                                        </div>
                                        <div class="d-flex align-items-center" style="gap:12px;flex-wrap:wrap">
                                            <span style="font-size:14px;font-weight:500">{{ __('translate.Daily Limit') }}:</span>
                                            <input type="number" id="dailyLimitInput" class="form-control form-control-sm" style="width:120px" value="{{ $general_setting->daily_invoice_limit ?? 5 }}" min="1" max="100">
                                            <button type="button" id="saveLimitBtn" class="crancy-btn" style="padding:4px 14px;font-size:13px">{{ __('translate.Save') }}</button>

                                            <span style="font-size:14px;font-weight:500;margin-left:8px">{{ __('translate.Feature Status') }}:</span>
                                            <label class="crancy__item-switch" style="margin:0">
                                                <input type="checkbox" id="featureToggle" {{ ($general_setting->invoice_status ?? '1') == '1' ? 'checked' : '' }}>
                                                <span class="crancy__item-switch--slide crancy__item-switch--round"></span>
                                            </label>
                                            <span id="featureStatusLabel" style="font-size:13px;font-weight:600;color:{{ ($general_setting->invoice_status ?? '1') == '1' ? '#28a745' : '#dc3545' }}">
                                                {{ ($general_setting->invoice_status ?? '1') == '1' ? __('translate.Enabled') : __('translate.Disabled') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <table class="crancy-table__main crancy-table__main-v3 dataTable no-footer" id="dataTable">
                                    <thead class="crancy-table__head">
                                        <tr>
                                            <th>{{ __('translate.Invoice No') }}</th>
                                            <th>{{ __('translate.Client') }}</th>
                                            <th>{{ __('translate.Amount') }}</th>
                                            <th>{{ __('translate.Date') }}</th>
                                            <th>{{ __('translate.Status') }}</th>
                                            <th>{{ __('translate.Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="crancy-table__body">
                                        @forelse ($invoices as $index => $invoice)
                                        <tr class="odd">
                                            <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                            <td>{{ $invoice->client_name }}</td>
                                            <td>{{ number_format($invoice->total, 2) }} {{ $general_setting->currency_icon ?? '$' }}</td>
                                            <td>{{ $invoice->created_at->format('d M Y') }}</td>
                                            <td>
                                                @if ($invoice->status == 'enable')
                                                    <span class="badge bg-success text-white">{{ __('translate.Enable') }}</span>
                                                @else
                                                    <span class="badge bg-danger text-white">{{ __('translate.Disable') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('invoice.show', $invoice->id) }}" class="crancy-btn" target="_blank"><i class="fas fa-eye"></i> {{ __('translate.View') }}</a>
                                                <a onclick="itemDeleteConfrimation({{ $invoice->id }})" href="javascript:;" data-bs-toggle="modal" data-bs-target="#exampleModal" class="crancy-btn delete_danger_btn"><i class="fas fa-trash"></i> {{ __('translate.Delete') }}</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">{{ __('translate.No invoices found') }}</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('translate.Delete Confirmation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('translate.Are you realy want to delete this item?') }}</p>
                </div>
                <div class="modal-footer">
                    <form action="" id="item_delect_confirmation" class="delet_modal_form" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('translate.Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('translate.Yes, Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js_section')
    <script>
        "use strict"
        function itemDeleteConfrimation(id){
            $("#item_delect_confirmation").attr("action", '{{ url(config('admin.prefix').'/invoice/') }}' + "/" + id)
        }

        $('#saveLimitBtn').on('click', function() {
            const limit = $('#dailyLimitInput').val();
            $.ajax({
                url: '{{ route('admin.invoice.save-limit') }}',
                type: 'POST',
                data: { limit: limit, _token: '{{ csrf_token() }}' },
                success: function(res) {
                    toastr.success(res.message);
                },
                error: function() {
                    toastr.error('{{ __('translate.Something went wrong') }}');
                }
            });
        });

        $('#featureToggle').on('change', function() {
            const status = $(this).is(':checked') ? 1 : 0;
            $.ajax({
                url: '{{ route('admin.invoice.toggle-feature') }}',
                type: 'POST',
                data: {
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    const label = $('#featureStatusLabel');
                    if (status == 1) {
                        label.text('{{ __('translate.Enabled') }}').css('color', '#28a745');
                    } else {
                        label.text('{{ __('translate.Disabled') }}').css('color', '#dc3545');
                    }
                    toastr.success(res.message);
                },
                error: function() {
                    toastr.error('{{ __('translate.Something went wrong') }}');
                    $('#featureToggle').prop('checked', !status);
                }
            });
        });
    </script>
@endpush
