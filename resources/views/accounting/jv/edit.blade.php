@extends('layouts.app')

@section('title', __('messages.edit_journal_voucher'))

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('messages.edit_journal_voucher') }} #{{ $jv->voucher_number }}</h1>
            <a href="{{ route('accounting.gl.transactions.jv.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back') }}
            </a>
        </div>

        <form action="{{ route('accounting.gl.transactions.jv.update', $jv->id) }}" method="POST" id="jv-form">
            @csrf
            @method('PUT')
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('messages.jv_date') }} <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="voucher_date"
                                class="form-control @error('voucher_date') is-invalid @enderror"
                                value="{{ old('voucher_date', $jv->voucher_date->format('Y-m-d')) }}" required>
                            @error('voucher_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">{{ __('messages.description') }}</label>
                            <textarea name="description" class="form-control"
                                rows="1">{{ old('description', $jv->description) }}</textarea>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="items-table">
                            <thead class="table-light">
                                <tr>
                                    <th width="20%">{{ __('messages.main_account') }}</th>
                                    <th width="20%">{{ __('messages.sub_account') }}</th>
                                    <th width="20%">{{ __('messages.details') }}</th>
                                    <th width="10%">{{ __('messages.debit') }}</th>
                                    <th width="10%">{{ __('messages.credit') }}</th>
                                    <th>{{ __('messages.notes') }}</th>
                                    <th width="50"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jv->items as $index => $item)
                                    @php
                                        // Fallback logic if main_account_id is null (migration phase)
                                        $mainAccountId = $item->main_account_id ?? ($item->account->parent_id ?? $item->account->id);
                                    @endphp
                                    <tr class="item-row">
                                        <td>
                                            <select name="items[{{ $index }}][main_account_id]"
                                                class="form-select select2 main-account-select" required
                                                data-row="{{ $index }}">
                                                <option value="">{{ __('messages.select_main_account') }}</option>
                                                <!-- Populated by JS -->
                                            </select>
                                            <input type="hidden" class="initial-main-id" value="{{ $mainAccountId }}">
                                        </td>
                                        <td>
                                            <select name="items[{{ $index }}][chart_of_account_id]"
                                                class="form-select select2 sub-account-select" required>
                                                <option value="">{{ __('messages.select_sub_account') }}</option>
                                                <!-- Populated by JS -->
                                            </select>
                                            <input type="hidden" class="initial-sub-id"
                                                value="{{ $item->chart_of_account_id }}">
                                        </td>
                                        <td class="entity-cell">
                                            <div class="input-group">
                                                <!-- Customer Select -->
                                                <select name="items[{{ $index }}][customer_id]"
                                                    class="form-select select2-ajax customer-select d-none"
                                                    data-type="customer">
                                                    <option value="">{{ __('messages.select_customer') }}</option>
                                                    @if($item->customer_id)
                                                        <option value="{{ $item->customer_id }}" selected>
                                                            {{ $item->customer->name_en ?? $item->customer->name_ar ?? $item->customer->name ?? '' }}
                                                        </option>
                                                    @endif
                                                </select>
                                                <!-- Vendor Select -->
                                                <select name="items[{{ $index }}][vendor_id]"
                                                    class="form-select select2-ajax vendor-select d-none" data-type="vendor">
                                                    <option value="">{{ __('messages.select_vendor') }}</option>
                                                    @if($item->vendor_id)
                                                        <option value="{{ $item->vendor_id }}" selected>
                                                            {{ $item->vendor->name_en ?? $item->vendor->name_ar ?? $item->vendor->name ?? '' }}
                                                        </option>
                                                    @endif
                                                </select>
                                                <button type="button" class="btn btn-outline-secondary add-entity-btn d-none"
                                                    title="{{ __('messages.add_new') }}">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td><input type="number" name="items[{{ $index }}][debit]"
                                                class="form-control debit-input" step="0.01" value="{{ $item->debit }}" min="0">
                                        </td>
                                        <td><input type="number" name="items[{{ $index }}][credit]"
                                                class="form-control credit-input" step="0.01" value="{{ $item->credit }}"
                                                min="0"></td>
                                        <td><input type="text" name="items[{{ $index }}][notes]" class="form-control"
                                                value="{{ $item->notes }}"></td>
                                        <td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i
                                                    class="fas fa-times"></i></button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">{{ __('messages.total') }}</td>
                                    <td id="total-debit" class="fw-bold">0.00</td>
                                    <td id="total-credit" class="fw-bold">0.00</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-row">
                        <i class="fas fa-plus me-1"></i> {{ __('messages.add_row') }}
                    </button>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <div id="balance-warning" class="text-danger fw-bold d-none">
                        <i class="fas fa-exclamation-triangle me-1"></i> {{ __('messages.jv_unbalanced') }}
                    </div>
                    <button type="submit" class="btn btn-primary px-5 ms-auto">
                        <i class="fas fa-save me-1"></i> {{ __('messages.update') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const accountsData = @json($accounts);
                const tableBody = document.querySelector('#items-table tbody');
                const addRowBtn = document.querySelector('#add-row');
                const totalDebitEl = document.querySelector('#total-debit');
                const totalCreditEl = document.querySelector('#total-credit');
                const balanceWarning = document.querySelector('#balance-warning');
                let rowCount = {{ count($jv->items) }};

                function populateMainAccounts(selectElement, selectedValue = null) {
                    const mainAccounts = accountsData.filter(acc => !acc.parent_id);
                    // Clear existing (except default)
                    while (selectElement.options.length > 1) {
                        selectElement.remove(1);
                    }

                    mainAccounts.forEach(acc => {
                        const option = new Option(`${acc.code} - ${@json(app()->getLocale() === 'ar') ? (acc.name_ar || acc.name_en) : (acc.name_en || acc.name_ar)}`, acc.id);
                        option.dataset.subLedgerType = acc.sub_ledger_type;
                        if (selectedValue && acc.id == selectedValue) {
                            option.selected = true;
                        }
                        selectElement.add(option);
                    });
                    // Trigger Select2 update if initialized
                    if ($(selectElement).hasClass("select2-hidden-accessible")) {
                        $(selectElement).trigger('change');
                    }
                }

                function populateSubAccounts(mainId, subSelectElement, selectedValue = null) {
                    subSelectElement.innerHTML = '<option value="">{{ __('messages.select_sub_account') }}</option>';
                    if (!mainId) {
                        subSelectElement.disabled = true;
                        return;
                    }

                    // Robust recursive finder using flat list
                    function getAllChildren(parentId) {
                        // Find direct children in the flat accountsData array
                        const directChildren = accountsData.filter(acc => acc.parent_id == parentId);
                        let allDescendants = [...directChildren];

                        directChildren.forEach(child => {
                            allDescendants = allDescendants.concat(getAllChildren(child.id));
                        });
                        return allDescendants;
                    }

                    const children = getAllChildren(mainId);
                    const mainAccount = accountsData.find(acc => acc.id == mainId);

                    if (children.length > 0) {
                        children.forEach(acc => {
                            const option = new Option(`${acc.code} - ${@json(app()->getLocale() === 'ar') ? (acc.name_ar || acc.name_en) : (acc.name_en || acc.name_ar)}`, acc.id);
                            option.dataset.subLedgerType = acc.sub_ledger_type;
                            if (!acc.is_posting_allowed) {
                                option.disabled = true;
                                option.text += ' ({{ __("messages.not_posting") }})';
                            }
                            if (selectedValue && acc.id == selectedValue) {
                                option.selected = true;
                            }
                            subSelectElement.add(option);
                        });
                    }

                    // Always add the Main Account itself
                    if (mainAccount) {
                        const option = new Option(`${mainAccount.code} - ${@json(app()->getLocale() === 'ar') ? (mainAccount.name_ar || mainAccount.name_en) : (mainAccount.name_en || mainAccount.name_ar)}`, mainAccount.id);
                        option.dataset.subLedgerType = mainAccount.sub_ledger_type;

                        if (!mainAccount.is_posting_allowed) {
                            option.disabled = true;
                            option.text += ' ({{ __("messages.not_posting") }})';
                        }

                        if (selectedValue && mainAccount.id == selectedValue) {
                            option.selected = true;
                        }

                        subSelectElement.add(option);
                    }

                    if (subSelectElement.options.length > 1) {
                        subSelectElement.disabled = false;
                    } else {
                        subSelectElement.disabled = true;
                    }

                    // Trigger Select2 update if initialized
                    if ($(subSelectElement).hasClass("select2-hidden-accessible")) {
                        $(subSelectElement).trigger('change');
                    }
                }

                function handleSubAccountChange(selectElement) {
                    const row = selectElement.closest('tr');
                    if (!selectElement || selectElement.selectedIndex < 0) return;

                    const selectedOption = selectElement.options[selectElement.selectedIndex];
                    const subLedgerType = (selectedOption && selectedOption.dataset) ? selectedOption.dataset.subLedgerType : '';

                    const customerSelect = row.querySelector('.customer-select');
                    const vendorSelect = row.querySelector('.vendor-select');
                    const addEntityBtn = row.querySelector('.add-entity-btn');

                    // Reset
                    if (customerSelect) {
                        customerSelect.classList.add('d-none');
                        customerSelect.disabled = true;
                        if ($(customerSelect).data('select2')) {
                            $(customerSelect).next('.select2-container').addClass('d-none');
                        }
                    }
                    if (vendorSelect) {
                        vendorSelect.classList.add('d-none');
                        vendorSelect.disabled = true;
                        if ($(vendorSelect).data('select2')) {
                            $(vendorSelect).next('.select2-container').addClass('d-none');
                        }
                    }
                    if (addEntityBtn) {
                        addEntityBtn.classList.add('d-none');
                    }

                    if (subLedgerType === 'customer' && customerSelect) {
                        customerSelect.classList.remove('d-none');
                        customerSelect.disabled = false;
                        $(customerSelect).next('.select2-container').removeClass('d-none');
                        if (addEntityBtn) addEntityBtn.classList.remove('d-none');
                    } else if (subLedgerType === 'vendor' && vendorSelect) {
                        vendorSelect.classList.remove('d-none');
                        vendorSelect.disabled = false;
                        $(vendorSelect).next('.select2-container').removeClass('d-none');
                        if (addEntityBtn) addEntityBtn.classList.remove('d-none');
                    }
                }

                function initAjaxSelect2(element) {
                    const type = element.getAttribute('data-type');
                    const url = type === 'customer' ? "{{ route('ajax.customers.search') }}" : "{{ route('ajax.vendors.search') }}";

                    $(element).select2({
                        theme: 'bootstrap-5',
                        placeholder: type === 'customer' ? '{{ __('messages.select_customer') }}' : '{{ __('messages.select_vendor') }}',
                        allowClear: true,
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
                    // Initially hide container if element is hidden (d-none)
                    if (element.classList.contains('d-none')) {
                        $(element).next('.select2-container').addClass('d-none');
                    }
                }

                // Initialize existing rows

                // First init Ajax Select2 so that pre-selected options are rendered correctly
                document.querySelectorAll('.select2-ajax').forEach(select => {
                    initAjaxSelect2(select);
                });

                document.querySelectorAll('.item-row').forEach(row => {
                    const mainSelect = row.querySelector('.main-account-select');
                    const subSelect = row.querySelector('.sub-account-select');
                    const initialMainId = row.querySelector('.initial-main-id').value;
                    const initialSubId = row.querySelector('.initial-sub-id').value;

                    populateMainAccounts(mainSelect, initialMainId);
                    if (initialMainId) {
                        populateSubAccounts(initialMainId, subSelect, initialSubId);
                    }

                    // Trigger visibility for existing rows
                    handleSubAccountChange(subSelect);

                    // If hidden, ensure container is hidden (Select2 might force display block on init)
                    const customerSelect = row.querySelector('.customer-select');
                    const vendorSelect = row.querySelector('.vendor-select');

                    if (customerSelect.classList.contains('d-none')) {
                        $(customerSelect).next('.select2-container').addClass('d-none');
                    } else {
                        $(customerSelect).next('.select2-container').removeClass('d-none');
                    }

                    if (vendorSelect.classList.contains('d-none')) {
                        $(vendorSelect).next('.select2-container').addClass('d-none');
                    } else {
                        $(vendorSelect).next('.select2-container').removeClass('d-none');
                    }
                });

                // Initialize Select2 for all standard selects after population
                $('.select2').select2({ theme: 'bootstrap-5' });

                // Event delegation
                tableBody.addEventListener('change', function (e) {
                    if (e.target.classList.contains('main-account-select')) {
                        const row = e.target.closest('tr');
                        const subSelect = row.querySelector('.sub-account-select');
                        populateSubAccounts(e.target.value, subSelect);
                        handleSubAccountChange(subSelect);
                    } else if (e.target.classList.contains('sub-account-select')) {
                        handleSubAccountChange(e.target);
                    }
                });


                function calculateTotals() {
                    let debs = 0;
                    let creds = 0;
                    document.querySelectorAll('.debit-input').forEach(input => debs += parseFloat(input.value || 0));
                    document.querySelectorAll('.credit-input').forEach(input => creds += parseFloat(input.value || 0));

                    totalDebitEl.textContent = debs.toFixed(2);
                    totalCreditEl.textContent = creds.toFixed(2);

                    if (Math.abs(debs - creds) > 0.01) {
                        balanceWarning.classList.remove('d-none');
                    } else {
                        balanceWarning.classList.add('d-none');
                    }
                }

                setTimeout(calculateTotals, 100);

                tableBody.addEventListener('input', (e) => {
                    if (e.target.classList.contains('debit-input') || e.target.classList.contains('credit-input')) {
                        calculateTotals();
                    }
                });

                tableBody.addEventListener('click', (e) => {
                    const removeBtn = e.target.closest('.remove-row');
                    if (removeBtn) {
                        removeBtn.closest('tr').remove();
                        calculateTotals();
                    }

                    const addBtn = e.target.closest('.add-entity-btn');
                    if (addBtn) {
                        const row = addBtn.closest('tr');
                        const subSelect = row.querySelector('.sub-account-select');
                        if (!subSelect || subSelect.selectedIndex < 0) return;
                        
                        const selectedOption = subSelect.options[subSelect.selectedIndex];
                        const subLedgerType = selectedOption ? selectedOption.dataset.subLedgerType : '';
                        
                        let url = '';
                        if (subLedgerType === 'customer') url = "{{ route('sales.customers.create') }}";
                        else if (subLedgerType === 'vendor') url = "{{ route('purchases.vendors.create') }}";

                        if (url) window.open(url, '_blank');
                    }
                });

                addRowBtn.addEventListener('click', () => {
                    const newRow = document.createElement('tr');
                    newRow.className = 'item-row';
                    newRow.innerHTML = `
                                        <td>
                                            <select name="items[${rowCount}][main_account_id]" class="form-select select2 main-account-select" required data-row="${rowCount}">
                                                <option value="">{{ __('messages.select_main_account') }}</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="items[${rowCount}][chart_of_account_id]" class="form-select select2 sub-account-select" required disabled>
                                                <option value="">{{ __('messages.select_sub_account') }}</option>
                                            </select>
                                        </td>
                                        <td class="entity-cell">
                                            <div class="input-group">
                                                <select name="items[${rowCount}][customer_id]" class="form-select select2-ajax customer-select d-none" data-type="customer" disabled>
                                                    <option value="">{{ __('messages.select_customer') }}</option>
                                                </select>
                                                <select name="items[${rowCount}][vendor_id]" class="form-select select2-ajax vendor-select d-none" data-type="vendor" disabled>
                                                    <option value="">{{ __('messages.select_vendor') }}</option>
                                                </select>
                                                <button type="button" class="btn btn-outline-secondary add-entity-btn d-none" title="{{ __('messages.add_new') }}">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td><input type="number" name="items[${rowCount}][debit]" class="form-control debit-input" step="0.01" value="0.00" min="0"></td>
                                        <td><input type="number" name="items[${rowCount}][credit]" class="form-control credit-input" step="0.01" value="0.00" min="0"></td>
                                        <td><input type="text" name="items[${rowCount}][notes]" class="form-control"></td>
                                        <td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="fas fa-times"></i></button></td>
                                    `;
                    tableBody.appendChild(newRow);

                    const newMainSelect = newRow.querySelector('.main-account-select');
                    const newCustomerSelect = newRow.querySelector('.customer-select');
                    const newVendorSelect = newRow.querySelector('.vendor-select');

                    populateMainAccounts(newMainSelect);

                    // Initialize select2
                    if (typeof $ !== 'undefined' && $.fn.select2) {
                        $(newRow).find('.select2').select2({ theme: 'bootstrap-5' });
                        initAjaxSelect2(newCustomerSelect);
                        initAjaxSelect2(newVendorSelect);
                    }

                    rowCount++;
                });
            });
        </script>
    @endpush
@endsection