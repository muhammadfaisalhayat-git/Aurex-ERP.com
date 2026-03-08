@extends('layouts.app')

@section('title', __('messages.sm_configure_header'))

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a
                            href="{{ route('acp.system.user-header.index') }}">{{ __('messages.sm_user_header_config') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ __('messages.configure') }}</li>
                </ol>
            </nav>
            <h1 class="h3">{{ __('messages.sm_configure_header_for') }}: <span class="text-primary">{{ $user->name }}</span>
            </h1>
        </div>

        <div class="row">
            <div class="col-md-7">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-desktop me-2 text-primary"></i>
                            {{ __('messages.display_settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('acp.system.user-header.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label class="form-label fw-bold">{{ __('messages.custom_header_title') }}</label>
                                <input type="text" name="header_title" class="form-control"
                                    value="{{ old('header_title', $setting->header_title) }}"
                                    placeholder="{{ $user->name }}">
                                <div class="form-text small">{{ __('messages.custom_header_title_desc') }}</div>
                            </div>

                            <h6 class="fw-bold mb-3 border-bottom pb-2 text-muted small text-uppercase">
                                {{ __('messages.visibility_toggles') }}</h6>

                            <div class="d-flex flex-column gap-3 mb-4">
                                <div
                                    class="form-check form-switch p-3 bg-light rounded d-flex justify-content-between align-items-center">
                                    <div>
                                        <label class="form-check-label fw-bold mb-0"
                                            for="show_company">{{ __('messages.show_company_name') }}</label>
                                        <div class="small text-muted">{{ __('messages.show_company_name_desc') }}</div>
                                    </div>
                                    <input class="form-check-input ms-0 mt-0" type="checkbox" name="show_company" value="1"
                                        id="show_company" {{ $setting->show_company ? 'checked' : '' }}>
                                </div>

                                <div
                                    class="form-check form-switch p-3 bg-light rounded d-flex justify-content-between align-items-center">
                                    <div>
                                        <label class="form-check-label fw-bold mb-0"
                                            for="show_branch">{{ __('messages.show_branch_name') }}</label>
                                        <div class="small text-muted">{{ __('messages.show_branch_name_desc') }}</div>
                                    </div>
                                    <input class="form-check-input ms-0 mt-0" type="checkbox" name="show_branch" value="1"
                                        id="show_branch" {{ $setting->show_branch ? 'checked' : '' }}>
                                </div>

                                <div
                                    class="form-check form-switch p-3 bg-light rounded d-flex justify-content-between align-items-center">
                                    <div>
                                        <label class="form-check-label fw-bold mb-0"
                                            for="show_date">{{ __('messages.show_current_date') }}</label>
                                        <div class="small text-muted">{{ __('messages.show_current_date_desc') }}</div>
                                    </div>
                                    <input class="form-check-input ms-0 mt-0" type="checkbox" name="show_date" value="1"
                                        id="show_date" {{ $setting->show_date ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('acp.system.user-header.index') }}"
                                    class="btn btn-outline-secondary">{{ __('messages.cancel') }}</a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-1"></i> {{ __('messages.save_config') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card shadow-sm border-0 sticky-top" style="top: 100px;">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-eye me-2 text-info"></i> {{ __('messages.live_preview') }}
                        </h5>
                    </div>
                    <div class="card-body bg-dark rounded-bottom p-4">
                        <div id="header-preview" class="p-3 border border-secondary rounded bg-white shadow-lg">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle p-2 me-2"
                                        style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user text-white small"></i>
                                    </div>
                                    <div>
                                        <div id="preview-title" class="fw-bold mb-0" style="font-size: 14px;">
                                            {{ $setting->header_title ?: $user->name }}</div>
                                        <div class="preview-meta small text-muted" style="font-size: 11px;">
                                            <span id="preview-company"
                                                class="{{ $setting->show_company ? '' : 'd-none' }}">{{ $user->company->name ?? 'Company Name' }}</span>
                                            <span id="preview-branch" class="{{ $setting->show_branch ? '' : 'd-none' }}"> |
                                                {{ $user->branch->name ?? 'Main Branch' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div id="preview-date" class="small text-muted {{ $setting->show_date ? '' : 'd-none' }}"
                                    style="font-size: 11px;">
                                    {{ date('Y-m-d') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const titleInput = document.querySelector('input[name="header_title"]');
                const companySwitch = document.getElementById('show_company');
                const branchSwitch = document.getElementById('show_branch');
                const dateSwitch = document.getElementById('show_date');

                const previewTitle = document.getElementById('preview-title');
                const previewCompany = document.getElementById('preview-company');
                const previewBranch = document.getElementById('preview-branch');
                const previewDate = document.getElementById('preview-date');

                const defaultName = "{{ $user->name }}";

                titleInput.addEventListener('input', function () {
                    previewTitle.textContent = this.value || defaultName;
                });

                companySwitch.addEventListener('change', function () {
                    previewCompany.classList.toggle('d-none', !this.checked);
                });

                branchSwitch.addEventListener('change', function () {
                    previewBranch.classList.toggle('d-none', !this.checked);
                });

                dateSwitch.addEventListener('change', function () {
                    previewDate.classList.toggle('d-none', !this.checked);
                });
            });
        </script>
    @endpush
@endsection