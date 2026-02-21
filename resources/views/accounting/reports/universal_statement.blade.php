@extends('layouts.app')

@section('title', __('messages.universal_statement_report'))

@section('content')
    <div class="container-fluid px-4">
        <h1 class="h3 mb-4 text-gray-800">{{ __('messages.universal_statement_report') }}</h1>

        <div class="row">
            <div class="col-xl-6 col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.filter') }}</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('accounting.gl.reports.universal-statement.generate') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.entity_type') }}</label>
                                <select name="entity_type" id="entity-type" class="form-select" required>
                                    <option value="">{{ __('messages.select_entity_type') }}</option>
                                    @foreach($entityTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3 d-none" id="entity-wrapper">
                                <label class="form-label" id="entity-label">{{ __('messages.entity') }}</label>
                                <select name="entity_id" id="entity-select" class="form-control select2-ajax" required>
                                    <option value="">{{ __('messages.select_entity') }}</option>
                                </select>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('messages.date_from') }}</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-01') }}"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('messages.date_to') }}</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}"
                                        required>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-file-alt me-1"></i> {{ __('messages.generate') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function () {
                // Setup CSRF for all AJAX requests
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $('#entity-type').change(function () {
                    const type = $(this).val();
                    const wrapper = $('#entity-wrapper');
                    const select = $('#entity-select');
                    const label = $('#entity-label');

                    if (type) {
                        wrapper.removeClass('d-none');
                        select.val(null).trigger('change');

                        // Update label based on selection
                        const typeLabel = $(this).find('option:selected').text();
                        label.text(typeLabel);

                        // Clear and re-initialize Select2 for the new type
                        if (select.data('select2')) {
                            select.select2('destroy');
                        }
                        select.empty().append('<option value="">{{ __("messages.select_entity") }}</option>');

                        select.select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            minimumInputLength: 0,
                            ajax: {
                                url: '{{ route("accounting.gl.reports.universal-statement.search") }}',
                                dataType: 'json',
                                delay: 250,
                                data: function (params) {
                                    return {
                                        type: type,
                                        q: params.term
                                    };
                                },
                                processResults: function (data) {
                                    return {
                                        results: data
                                    };
                                }
                            }
                        });
                    } else {
                        wrapper.addClass('d-none');
                        if (select.data('select2')) {
                            select.select2('destroy');
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection