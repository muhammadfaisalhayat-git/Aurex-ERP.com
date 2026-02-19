@extends('layouts.app')

@section('title', __('messages.account_explorer'))

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('messages.account_explorer') }}</h1>
        </div>

        <div class="row g-4 explorer-container">
            <!-- Main Accounts Column -->
            <div class="col-md-3">
                <div class="card shadow-sm h-100 explorer-card">
                    <div class="card-header bg-primary text-white py-3">
                        <h6 class="m-0 font-weight-bold"><i class="fas fa-list-ul me-2"></i>
                            {{ __('messages.main_accounts') }}</h6>
                    </div>
                    <div class="card-body p-0 explorer-list" id="main-accounts-list">
                        @foreach($mainAccounts as $account)
                            <div class="list-group-item list-group-item-action explorer-item border-0 border-bottom d-flex justify-content-between align-items-center"
                                data-id="{{ $account->id }}" data-type="main" onclick="loadSubAccounts(this)">
                                <span class="account-label">
                                    <code class="text-primary me-2">{{ $account->code }}</code>
                                    {{ $isRtl ? ($account->name_ar ?? $account->name_en) : ($account->name_en ?? $account->name_ar) }}
                                </span>
                                @if($account->children_count > 0)
                                    <i class="fas fa-chevron-right text-muted icon-chevron"></i>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sub Accounts Column -->
            <div class="col-md-3">
                <div class="card shadow-sm h-100 explorer-card">
                    <div class="card-header bg-info text-white py-3">
                        <h6 class="m-0 font-weight-bold"><i class="fas fa-sitemap me-2"></i>
                            {{ __('messages.sub_accounts') }}</h6>
                    </div>
                    <div class="card-body p-0 explorer-list" id="sub-accounts-list">
                        <div class="p-4 text-center text-muted placeholder-text">
                            <i class="fas fa-arrow-left fa-2x mb-2 d-block"></i>
                            {{ __('messages.select_main_account') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Data Column -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100 explorer-card">
                    <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold" id="selected-account-title"><i class="fas fa-info-circle me-2"></i>
                            {{ __('messages.account_details') }}</h6>
                        <div id="account-actions" class="d-none">
                            <span class="badge bg-light text-dark" id="account-code-badge"></span>
                        </div>
                    </div>
                    <div class="card-body explorer-data" id="account-data-container">
                        <div class="p-5 text-center text-muted placeholder-text">
                            <i class="fas fa-chart-line fa-3x mb-3 d-block op-2"></i>
                            {{ __('messages.select_account_to_view_data') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .explorer-container {
            height: calc(100vh - 160px);
        }

        .explorer-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }

        .explorer-list {
            overflow-y: auto;
            max-height: 100%;
        }

        .explorer-item {
            cursor: pointer;
            padding: 1rem 1.25rem;
            transition: background 0.2s;
        }

        .explorer-item:hover {
            background-color: #f8f9fa;
        }

        .explorer-item.active {
            background-color: #e9ecef;
            border-left: 4px solid #4e73df !important;
        }

        .explorer-item.active .account-label {
            font-weight: 600;
        }

        .icon-chevron {
            font-size: 0.8rem;
            transition: transform 0.2s;
        }

        .active .icon-chevron {
            transform: translateX(3px);
            color: #4e73df !important;
        }

        .placeholder-text {
            opacity: 0.6;
        }

        .op-2 {
            opacity: 0.2;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .data-fade-in {
            animation: fadeIn 0.4s ease forwards;
        }

        .summary-card {
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
    </style>

    <script>
        function loadSubAccounts(element) {
            const accountId = element.dataset.id;
            const parentContainer = element.closest('.explorer-list');

            // Clear sub-accounts and data
            document.getElementById('sub-accounts-list').innerHTML = `
                        <div class="p-4 text-center">
                            <div class="spinner-border text-info spinner-border-sm" role="status"></div>
                            <span class="ms-2">Loading...</span>
                        </div>
                    `;
            document.getElementById('account-data-container').innerHTML = `
                        <div class="p-5 text-center text-muted placeholder-text">
                            <i class="fas fa-chart-line fa-3x mb-3 d-block op-2"></i>
                            {{ __('messages.select_account_to_view_data') }}
                        </div>
                    `;

            // Toggle active state
            parentContainer.querySelectorAll('.explorer-item').forEach(i => i.classList.remove('active'));
            element.classList.add('active');

            fetch(`/ajax/accounting/sub-accounts/${accountId}`)
                .then(response => response.json())
                .then(data => {
                    const list = document.getElementById('sub-accounts-list');
                    list.innerHTML = '';

                    if (data.length === 0) {
                        list.innerHTML = `
                                    <div class="p-4 text-center text-muted">
                                        No sub-accounts found.
                                        <button class="btn btn-sm btn-link mt-2 d-block mx-auto" onclick="loadAccountDataDirect(${accountId}, '${element.querySelector('.account-label').innerText}')">
                                            Load Data for this Account
                                        </button>
                                    </div>`;
                        return;
                    }

                    data.forEach(sub => {
                        const item = document.createElement('div');
                        item.className = 'list-group-item list-group-item-action explorer-item border-0 border-bottom d-flex justify-content-between align-items-center';
                        item.dataset.id = sub.id;
                        item.onclick = () => loadAccountDetails(item);

                        const label = document.createElement('span');
                        label.className = 'account-label';
                        const locale = '{{ app()->getLocale() }}';
                        const name = locale === 'ar' ? (sub.name_ar || sub.name_en) : (sub.name_en || sub.name_ar);
                        label.innerHTML = `<code class="text-info me-2">${sub.code}</code> ${name}`;

                        item.appendChild(label);

                        if (sub.children_count > 0) {
                            item.innerHTML += '<i class="fas fa-chevron-right text-muted icon-chevron"></i>';
                        }

                        list.appendChild(item);
                    });
                });
        }

        function loadAccountDetails(element) {
            const accountId = element.dataset.id;
            const parentContainer = element.closest('.explorer-list');

            // Toggle active state
            parentContainer.querySelectorAll('.explorer-item').forEach(i => i.classList.remove('active'));
            element.classList.add('active');

            loadAccountData(accountId);
        }

        function loadAccountDataDirect(id, name) {
            loadAccountData(id);
        }

        function loadAccountData(accountId) {
            const container = document.getElementById('account-data-container');
            container.innerHTML = `
                        <div class="p-5 text-center">
                            <div class="spinner-border text-dark" role="status"></div>
                            <div class="mt-2 text-muted">{{ __('messages.fetching_transactions') }}</div>
                        </div>
                    `;

            fetch(`/ajax/accounting/account-data/${accountId}`)
                .then(response => response.json())
                .then(res => {
                    const account = res.account;
                    const entries = res.entries;
                    const summary = res.summary;

                    // Update Header
                    document.getElementById('selected-account-title').innerHTML = `
                                <i class="fas fa-file-invoice-dollar me-2"></i> 
                                ${appLocale === 'ar' ? (account.name_ar || account.name_en) : (account.name_en || account.name_ar)}
                            `;
                    document.getElementById('account-actions').classList.remove('d-none');
                    document.getElementById('account-code-badge').innerText = account.code;

                    let html = `
                                <div class="data-fade-in">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="mb-0 text-muted small fw-bold uppercase">{{ __('messages.financial_summary') }}</h5>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <div class="bg-light summary-card">
                                                <small class="text-muted d-block uppercase small fw-bold">{{ __('messages.total_debit') }}</small>
                                                <h4 class="mb-0 text-primary">${summary.total_debit.toFixed(2)}</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="bg-light summary-card">
                                                <small class="text-muted d-block uppercase small fw-bold">{{ __('messages.total_credit') }}</small>
                                                <h4 class="mb-0 text-danger">${summary.total_credit.toFixed(2)}</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="summary-card ${summary.balance >= 0 ? 'bg-success text-white' : 'bg-warning text-dark'}">
                                                <small class="d-block uppercase small fw-bold">{{ __('messages.current_balance') }}</small>
                                                <h4 class="mb-0">${summary.balance.toFixed(2)}</h4>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>{{ __('messages.date') }}</th>
                                                    <th>{{ __('messages.reference') }}</th>
                                                    <th>{{ __('messages.description') }}</th>
                                                    <th class="text-end">{{ __('messages.debit') }}</th>
                                                    <th class="text-end">{{ __('messages.credit') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                            `;

                    if (entries.length === 0) {
                        html += `<tr><td colspan="5" class="text-center py-4 text-muted">{{ __('messages.no_transactions_found') }}</td></tr>`;
                    } else {
                        entries.forEach(entry => {
                            html += `
                                        <tr>
                                            <td>${entry.transaction_date}</td>
                                            <td><span class="badge border text-dark font-monospace">${entry.reference_number}</span></td>
                                            <td>
                                                <small class="text-muted d-block">${entry.description || '--'}</small>
                                                ${entry.customer ? `<span class="badge bg-soft-primary text-primary">C: ${entry.customer.name}</span>` : ''}
                                                ${entry.vendor ? `<span class="badge bg-soft-info text-info">V: ${entry.vendor.name}</span>` : ''}
                                            </td>
                                            <td class="text-end font-monospace">${entry.debit > 0 ? entry.debit.toFixed(2) : '-'}</td>
                                            <td class="text-end font-monospace text-danger">${entry.credit > 0 ? entry.credit.toFixed(2) : '-'}</td>
                                        </tr>
                                    `;
                        });
                    }

                    html += `
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            `;
                    container.innerHTML = html;
                });
        }

        const appLocale = '{{ app()->getLocale() }}';
    </script>
@endsection