@extends('layouts.app')

@section('title', __('messages.daily_ledger'))

@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h4 mb-1 fw-bold">{{ __('messages.daily_ledger') }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a
                                href="{{ route('accounting.gl.dashboard') }}">{{ __('messages.accounting_system') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('messages.reports') }}</li>
                        <li class="breadcrumb-item active">{{ __('messages.daily_ledger') }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>{{ __('messages.print') }}
                </button>
                <button type="button" class="btn btn-outline-success rounded-pill px-4" id="btn-excel">
                    <i class="fas fa-file-excel me-2"></i>{{ __('messages.export_to_excel') }}
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-4" id="btn-pdf">
                    <i class="fas fa-file-pdf me-2"></i>{{ __('messages.export_to_pdf') }}
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <form id="filter-form" class="row g-3">
                    @csrf
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">{{ __('messages.ledger_date_from') }}</label>
                        <input type="date" name="start_date" class="form-control rounded-3" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">{{ __('messages.ledger_date_to') }}</label>
                        <input type="date" name="end_date" class="form-control rounded-3" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">{{ __('messages.ledger_branch') }}</label>
                        <select name="branch_id" class="form-select rounded-3 select2">
                            <option value="">{{ __('messages.select_branch') }}</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">{{ __('messages.ledger_account') }}</label>
                        <select name="chart_of_account_id" class="form-select rounded-3 select2">
                            <option value="">{{ __('messages.select_account') }}</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">{{ __('messages.ledger_customer') }}</label>
                        <select name="customer_id" class="form-select rounded-3 select2">
                            <option value="">{{ __('messages.select_customer') }}</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">{{ __('messages.ledger_vendor') }}</label>
                        <select name="vendor_id" class="form-select rounded-3 select2">
                            <option value="">{{ __('messages.select_vendor') }}</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold">
                            <i class="fas fa-search me-2"></i>{{ __('messages.ledger_search') }}
                        </button>
                        <button type="reset" class="btn btn-light px-4 rounded-pill ms-2">
                            {{ __('messages.ledger_reset') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="ledger-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">{{ __('messages.date') }}</th>
                                <th>{{ __('messages.ledger_reference') }}</th>
                                <th>{{ __('messages.account_name') }}</th>
                                <th>{{ __('messages.ledger_sub_account') }}</th>
                                <th>{{ __('messages.description') }}</th>
                                <th class="text-end">{{ __('messages.debit') }}</th>
                                <th class="text-end">{{ __('messages.credit') }}</th>
                                <th class="text-end pe-4">{{ __('messages.ledger_running_balance') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-info-circle me-2"></i>{{ __('messages.ledger_please_wait') }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-light fw-bold border-top-2">
                            <tr id="opening-balance-row">
                                <td colspan="7" class="text-end ps-4 py-3">{{ __('messages.ledger_opening_balance') }}:</td>
                                <td class="text-end pe-4" id="opening-balance">0.00</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end ps-4 py-3 text-uppercase small">{{ __('messages.total') }}:
                                </td>
                                <td class="text-end" id="total-debit">0.00</td>
                                <td class="text-end" id="total-credit">0.00</td>
                                <td class="text-end pe-4 text-primary" id="ledger_net_movement">0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('.select2').select2({
                    theme: 'bootstrap-5'
                });

                const fetchLedger = () => {
                    const formData = $('#filter-form').serialize();
                    const tbody = $('#ledger-table tbody');

                    tbody.html('<tr><td colspan="8" class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></td></tr>');

                    $.ajax({
                        url: "{{ route('accounting.gl.reports.daily-ledger.fetch') }}",
                        type: "POST",
                        data: formData,
                        success: function (response) {
                            tbody.empty();

                            $('#opening-balance').text(response.opening_balance.toLocaleString(undefined, { minimumFractionDigits: 2 }));
                            $('#total-debit').text(response.total_debit.toLocaleString(undefined, { minimumFractionDigits: 2 }));
                            $('#total-credit').text(response.total_credit.toLocaleString(undefined, { minimumFractionDigits: 2 }));
                            $('#net-movement').text(response.net_movement.toLocaleString(undefined, { minimumFractionDigits: 2 }));

                            if (response.entries.length === 0) {
                                tbody.append('<tr><td colspan="8" class="text-center py-4">{{ __("messages.no_data_found") }}</td></tr>');
                                return;
                            }

                            response.entries.forEach(entry => {
                                const subAccount = entry.customer ? entry.customer.name : (entry.vendor ? entry.vendor.name : '-');
                                const row = `
                                <tr>
                                    <td class="ps-4">${entry.transaction_date}</td>
                                    <td class="fw-medium">${entry.reference_number || '-'}</td>
                                    <td>
                                        <div class="fw-semibold">${entry.chart_of_account.name}</div>
                                        <div class="small text-muted">${entry.chart_of_account.code}</div>
                                    </td>
                                    <td>${subAccount}</td>
                                    <td><small class="text-muted">${entry.description || ''}</small></td>
                                    <td class="text-end fw-semibold">${entry.debit > 0 ? entry.debit.toLocaleString(undefined, { minimumFractionDigits: 2 }) : '-'}</td>
                                    <td class="text-end fw-semibold text-danger">${entry.credit > 0 ? entry.credit.toLocaleString(undefined, { minimumFractionDigits: 2 }) : '-'}</td>
                                    <td class="text-end pe-4 fw-bold text-primary">${entry.running_balance.toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                                </tr>
                            `;
                                tbody.append(row);
                            });
                        },
                        error: function () {
                            tbody.html('<tr><td colspan="8" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error loading data.</td></tr>');
                        }
                    });
                };

                $('#filter-form').on('submit', function (e) {
                    e.preventDefault();
                    fetchLedger();
                });

                // Initial fetch
                fetchLedger();
            });
        </script>
    @endpush

    <style>
        @media print {

            .navbar,
            .sidebar,
            #filter-form,
            .btn-primary,
            .btn-outline-secondary,
            .btn-outline-success,
            .breadcrumb {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            .container-fluid {
                padding: 0 !important;
            }

            .table {
                width: 100% !important;
            }
        }
    </style>
@endsection