@extends('layouts.app')

@section('title', __('messages.create_account'))

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('messages.create_account') }}</h1>
            <a href="{{ route('accounting.gl.coa.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('accounting.gl.coa.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('messages.name_en') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror"
                                value="{{ old('name_en') }}" required>
                            @error('name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('messages.name_ar') }}</label>
                            <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror"
                                value="{{ old('name_ar') }}">
                            @error('name_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('messages.account_type') }} <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <select name="account_type_id" id="account_type_id" class="form-select @error('account_type_id') is-invalid @enderror" requried>
                                    <option value="">{{ __('messages.select_type') }}</option>
                                    @foreach($accountTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('account_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addAccountTypeModal">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="editAccountTypeBtn" disabled>
                                    <i class="fas fa-edit"></i>
                                </button>
                                @error('account_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('messages.parent_account') }}</label>
                            <div class="input-group">
                                <select name="parent_id" class="form-select select2 @error('parent_id') is-invalid @enderror">
                                    <option value="">{{ __('messages.main_account') }}</option>
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->code }} - {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <a href="{{ route('accounting.gl.coa.create') }}" class="btn btn-outline-secondary" target="_blank">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </div>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('messages.sub_ledger_type') }}</label>
                            <select name="sub_ledger_type" class="form-select @error('sub_ledger_type') is-invalid @enderror">
                                <option value="">{{ __('messages.none') }}</option>
                                <option value="customer" {{ old('sub_ledger_type') == 'customer' ? 'selected' : '' }}>{{ __('messages.customer') }}</option>
                                <option value="vendor" {{ old('sub_ledger_type') == 'vendor' ? 'selected' : '' }}>{{ __('messages.vendor') }}</option>
                                <option value="employee" {{ old('sub_ledger_type') == 'employee' ? 'selected' : '' }}>{{ __('messages.employee') }}</option>
                            </select>
                            <small class="text-muted">{{ __('messages.sub_ledger_type_help') }}</small>
                            @error('sub_ledger_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="is_posting_allowed"
                                    id="is_posting_allowed" value="1" {{ old('is_posting_allowed', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_posting_allowed">
                                    {{ __('messages.is_posting_allowed') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fas fa-save me-1"></i> {{ __('messages.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    <!-- Add Account Type Modal -->
    <div class="modal fade" id="addAccountTypeModal" tabindex="-1" aria-labelledby="addAccountTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAccountTypeModalLabel">{{ __('messages.add_account_type') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addAccountTypeForm">
                        <div class="mb-3">
                            <label for="type_name_en" class="form-label">{{ __('messages.name_en') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="type_name_en" name="name_en" required>
                        </div>
                        <div class="mb-3">
                            <label for="type_name_ar" class="form-label">{{ __('messages.name_ar') }}</label>
                            <input type="text" class="form-control" id="type_name_ar" name="name_ar">
                        </div>
                        <div class="mb-3">
                            <label for="type_code" class="form-label">{{ __('messages.code') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="type_code" name="code" required>
                            <small class="text-muted">{{ __('messages.code_help_text') }}</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                    <button type="button" class="btn btn-primary" id="saveAccountType">{{ __('messages.save') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('turbo:load', function() {
        const saveBtn = document.getElementById('saveAccountType');
        const editBtn = document.getElementById('editAccountTypeBtn');
        const form = document.getElementById('addAccountTypeForm');
        const select = document.getElementById('account_type_id');
        const modalEl = document.getElementById('addAccountTypeModal');
        const modalTitle = document.getElementById('addAccountTypeModalLabel');
        const modal = new bootstrap.Modal(modalEl);
        let isEditMode = false;
        let currentTypeId = null;

        // Enable/Disable Edit button
        select.addEventListener('change', function() {
            editBtn.disabled = !this.value;
        });

        // Open Modal in Add Mode
        modalEl.addEventListener('show.bs.modal', function (event) {
            // Only reset if triggered by a button (the Add button)
            if (event.relatedTarget) {
                isEditMode = false;
                currentTypeId = null;
                form.reset();
                modalTitle.textContent = "{{ __('messages.add_account_type') }}";
            }
        });

        // Open Modal in Edit Mode
        editBtn.addEventListener('click', function() {
            const typeId = select.value;
            if (!typeId) return;

            // Fetch details
            fetch(`{{ url('accounting/gl/account-types') }}/${typeId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        isEditMode = true;
                        currentTypeId = typeId;
                        document.getElementById('type_name_en').value = data.data.name_en;
                        document.getElementById('type_name_ar').value = data.data.name_ar || '';
                        document.getElementById('type_code').value = data.data.code;
                        modalTitle.textContent = "{{ __('messages.edit_account_type') }}";
                        modal.show();
                    }
                })
                .catch(error => console.error('Error fetching type:', error));
        });

        saveBtn.addEventListener('click', function() {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            let url = "{{ route('accounting.gl.account-types.store') }}";
            let method = 'POST';

            if (isEditMode) {
                url = `{{ url('accounting/gl/account-types') }}/${currentTypeId}`;
                method = 'PUT';
            }

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (isEditMode) {
                        // Update existing option
                        const option = select.querySelector(`option[value="${data.data.id}"]`);
                        if (option) {
                            option.textContent = data.data.name_en; // Or handle locale
                        }
                    } else {
                        // Add new option
                        const option = new Option(data.data.name_en, data.data.id, true, true);
                        select.add(option);
                    }
                    modal.hide();
                    form.reset();
                } else {
                    alert('Error saving account type: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred.');
            });
        });
    });
</script>
@endpush