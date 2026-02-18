@extends('layouts.app')

@section('title', __('messages.account_statement_report'))

@section('content')
    <div class="container-fluid px-4">
        <h1 class="h3 mb-4 text-gray-800">{{ __('messages.account_statement_report') }}</h1>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.reports_filters') }}</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('accounting.gl.reports.account-statement') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.account') }} <span
                                        class="text-danger">*</span></label>
                                <select name="chart_of_account_id" class="form-select select2" required id="account-select">
                                    <option value="">{{ __('messages.select_account') }}</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}"
                                            data-sub-ledger-type="{{ $account->sub_ledger_type }}">
                                            {{ $account->code }} -
                                            {{ $isRtl ? ($account->name_ar ?? $account->name_en) : ($account->name_en ?? $account->name_ar) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('chart_of_account_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Sub Ledger Selectors -->
                            <div class="mb-3 d-none" id="customer-wrapper">
                                <label class="form-label">{{ __('messages.customer') }}</label>
                                <select name="customer_id" class="form-select select2-ajax" id="customer-select"
                                    data-type="customer">
                                    <option value="">{{ __('messages.select_customer') }}</option>
                                </select>
                            </div>

                            <div class="mb-3 d-none" id="vendor-wrapper">
                                <label class="form-label">{{ __('messages.vendor') }}</label>
                                <select name="vendor_id" class="form-select select2-ajax" id="vendor-select"
                                    data-type="vendor">
                                    <option value="">{{ __('messages.select_vendor') }}</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('messages.date_from') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-01') }}"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('messages.date_to') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}"
                                        required>
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-file-invoice-dollar me-1"></i> {{ __('messages.reports_view_report') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                // Function to initialize Ajax Select2
                function initAjaxSelect2(element) {
                    const type = $(element).data('type');
                    const url = type === 'customer' ? "{{ route('ajax.customers.search') }}" : "{{ route('ajax.vendors.search') }}";

                    $(element).select2({
                        theme: 'bootstrap-5',
                        placeholder: type === 'customer' ? '{{ __('messages.select_customer') }}' : '{{ __('messages.select_vendor') }}',
                        allowClear: true,
                        width: '100%',
                        ajax: {
                            url: url,
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return {
                                    q: params.term, // search term
                                    page: params.page
                                };
                            },
                            processResults: function (data) {
                                return {
                                    results: data.map(function (item) {
                                        return {
                                            id: item.id,
                                            text: item.name_en || item.name_ar || item.name
                                        };
                                    })
                                };
                            },
                            cache: true
                        }
                    });
                }

                initAjaxSelect2('#customer-select');
                initAjaxSelect2('#vendor-select');

                $('#account-select').change(function () {
                    const selectedOption = $(this).find('option:selected');
                    const subLedgerType = selectedOption.data('sub-ledger-type');

                    $('#customer-wrapper').addClass('d-none');
                    $('#vendor-wrapper').addClass('d-none');
                    $('#customer-select').val(null).trigger('change');
                    $('#vendor-select').val(null).trigger('change');

                    if (subLedgerType === 'customer') {
                        $('#customer-wrapper').removeClass('d-none');
                    } else if (subLedgerType === 'vendor') {
                        $('#vendor-wrapper').removeClass('d-none');
                    }
                });
            });
        </script>
    @endpush
@endsection