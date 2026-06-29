@extends('admin.master_layout')
@section('title')
    <title>{{ __('translate.Manage Files') }} — {{ $product->translate?->name ?? $product->name }}</title>
@endsection
@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Manage Files') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Manage Product') }} >> {{ $product->translate?->name ?? $product->name }} >> {{ __('translate.Files') }}</p>
@endsection
@section('body-content')
<section class="crancy-adashboard crancy-show">
    <div class="container container__bscreen">
        <div class="row">
            <div class="col-12">
                <div class="crancy-body">
                    <div class="crancy-dsinner">
                        <div class="row">
                            <div class="col-12 mg-top-30">
                                <div class="crancy-product-card">
                                    <div class="create_new_btn_inline_box">
                                        <h4 class="crancy-product-card__title">{{ __('translate.Upload New File') }}</h4>
                                    </div>
                                    <form action="{{ route('admin.product.files.upload', $product->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <label class="crancy__item-label">{{ __('translate.Version') }} *</label>
                                                    <input class="crancy__item-input" type="text" name="version" placeholder="e.g. 1.0.0" value="{{ old('version') }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <label class="crancy__item-label">{{ __('translate.File') }} *</label>
                                                    <input class="crancy__item-input" type="file" name="file" required>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="crancy__item-form--group mg-top-form-20">
                                                    <label class="crancy__item-label">{{ __('translate.Changelog') }}</label>
                                                    <textarea class="crancy__item-input crancy__item-textarea" name="changelog" rows="3" placeholder="{{ __('translate.What changed in this version?') }}">{{ old('changelog') }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-12 mg-top-15">
                                                <button class="crancy-btn" type="submit">{{ __('translate.Upload') }}</button>
                                                <a href="{{ route('admin.product.index') }}" class="crancy-btn crancy-btn__gray ml-2">{{ __('translate.Back to Products') }}</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="col-12 mg-top-30">
                                <div class="crancy-product-card">
                                    <div class="create_new_btn_inline_box">
                                        <h4 class="crancy-product-card__title">{{ __('translate.All Files') }}</h4>
                                    </div>
                                    <div class="crancy-table crancy-table--v3 mg-top-30">
                                        <table class="crancy-table__main crancy-table__main-v3">
                                            <thead class="crancy-table__head">
                                                <tr>
                                                    <th>{{ __('translate.Version') }}</th>
                                                    <th>{{ __('translate.File Name') }}</th>
                                                    <th>{{ __('translate.Size') }}</th>
                                                    <th>{{ __('translate.Downloads') }}</th>
                                                    <th>{{ __('translate.Current') }}</th>
                                                    <th>{{ __('translate.Date') }}</th>
                                                    <th>{{ __('translate.Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($product->files as $file)
                                                    <tr>
                                                        <td>{{ $file->version }}</td>
                                                        <td>{{ $file->file_name }}</td>
                                                        <td>{{ $file->fileSizeForHumans }}</td>
                                                        <td>{{ $file->download_count ?? 0 }}</td>
                                                        <td>
                                                            @if($file->is_current)
                                                                <span class="badge bg-success">{{ __('translate.Yes') }}</span>
                                                            @else
                                                                <a href="{{ route('admin.product.files.set-current', $file->id) }}" class="badge bg-secondary" onclick="return confirm('{{ __('translate.Set this as the current version?') }}')">{{ __('translate.Set Current') }}</a>
                                                            @endif
                                                        </td>
                                                        <td>{{ $file->created_at->format('M d, Y') }}</td>
                                                        <td>
                                                            <a href="{{ route('admin.product.files.delete', $file->id) }}" class="crancy-btn crancy-btn__danger" onclick="return confirm('{{ __('translate.Are you realy want to delete this item?') }}')">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">{{ __('translate.No files uploaded yet') }}</td>
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
            </div>
        </div>
    </div>
</section>
@endsection
