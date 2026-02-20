@extends('layouts.app')

@section('title', 'New Receipt Voucher')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">New Receipt Voucher</h1>
        <a href="{{ route('finance.vouchers.receipt.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('finance.vouchers.receipt.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Voucher Date <span class="text-danger">*</span></label>
                        <input type="date" name="voucher_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Bank/Cash Account <span class="text-danger">*</span></label>
                        <select name="bank_account_id" class="form-select" required>
                            <option value="">Select Account</option>
                            @foreach($bankAccounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name_en }} ({{ number_format($account->current_balance, 2) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="check">Check</option>
                            <option value="cash">Cash</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Payer Name <span class="text-danger">*</span></label>
                        <input type="text" name="payer_name" class="form-control" required placeholder="Who is paying you?">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Income/Source GL Account <span class="text-danger">*</span></label>
                        <select name="chart_of_account_id" id="target_account_id" class="form-select select2" required>
                            <option value="">Select Account</option>
                            @foreach($coaAccounts as $coa)
                                <option value="{{ $coa->id }}" data-sub-ledger="{{ $coa->sub_ledger_type }}">{{ $coa->code }} - {{ $coa->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Reference Number (Check # / Ref #)</label>
                        <input type="text" name="reference_number" class="form-control">
                    </div>
                </div>

                <div class="row" id="beneficiary_container" style="display: none;">
                    <div class="col-md-12 mb-3">
                        <div class="p-3 bg-light border rounded">
                            <label class="form-label fw-bold" id="beneficiary_label">Select Beneficiary (Mandatory for this Account) <span class="text-danger">*</span></label>
                            <select name="beneficiary_id" id="beneficiary_id" class="form-select select2">
                                <option value="">Select Account</option>
                            </select>
                            <input type="hidden" name="beneficiary_type" id="beneficiary_type">
                            <div class="form-text text-success" id="beneficiary_help">This account requires a specific entity for subsidiary ledger tracking.</div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description / Remarks</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Create Voucher</button>
                    <a href="{{ route('finance.vouchers.receipt.index') }}" class="btn btn-link text-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('turbo:load', function() {
        const targetAccountSelect = document.getElementById('target_account_id');
        const beneficiaryContainer = document.getElementById('beneficiary_container');
        const beneficiarySelect = document.getElementById('beneficiary_id');
        const beneficiaryTypeInput = document.getElementById('beneficiary_type');
        const beneficiaryLabel = document.getElementById('beneficiary_label');
        const beneficiaryHelp = document.getElementById('beneficiary_help');
        const payerInput = document.querySelector('input[name="payer_name"]');

        function loadBeneficiaries() {
            const accountId = targetAccountSelect.value;
            if (!accountId) {
                beneficiaryContainer.style.display = 'none';
                beneficiarySelect.innerHTML = '<option value="">Select Account</option>';
                beneficiarySelect.removeAttribute('required');
                return;
            }

            fetch(`/accounting/gl/coa/${accountId}/beneficiaries`)
                .then(response => response.json())
                .then(data => {
                    if (data.beneficiaries && data.beneficiaries.length > 0) {
                        beneficiaryContainer.style.display = 'block';
                        beneficiarySelect.setAttribute('required', 'required');
                        beneficiaryTypeInput.value = data.beneficiaries[0].type;
                        
                        // Update Label
                        const typeName = data.type.charAt(0).toUpperCase() + data.type.slice(1);
                        beneficiaryLabel.innerHTML = `Select ${typeName} (Mandatory for this Account) <span class="text-danger">*</span>`;
                        beneficiaryHelp.innerText = `This account requires a related ${data.type} for accurate ledger tracking.`;

                        // Populate Dropdown
                        let options = `<option value="">Select ${typeName}</option>`;
                        data.beneficiaries.forEach(item => {
                            options += `<option value="${item.id}">${item.name}</option>`;
                        });
                        beneficiarySelect.innerHTML = options;
                        
                        if (typeof $ !== 'undefined' && $(beneficiarySelect).data('select2')) {
                            $(beneficiarySelect).val('').trigger('change');
                        }
                    } else {
                        beneficiaryContainer.style.display = 'none';
                        beneficiarySelect.removeAttribute('required');
                        beneficiarySelect.innerHTML = '<option value="">Select Account</option>';
                        beneficiaryTypeInput.value = '';
                    }
                });
        }

        targetAccountSelect.addEventListener('change', loadBeneficiaries);

        beneficiarySelect.addEventListener('change', function() {
            if (this.value && !payerInput.value) {
                payerInput.value = this.options[this.selectedIndex].text;
            }
        });

        if (targetAccountSelect.value) {
            loadBeneficiaries();
        }
    });
</script>
@endpush
