@extends('layouts.app')

@section('title', __('customer_registration.create'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('customer_registration.create') }}</h1>
            <a href="{{ route('sales.customer-registrations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('general.back') }}
            </a>
        </div>

        <form action="{{ route('sales.customer-registrations.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0" id="info-card-header">{{ __('customer_registration.company_info') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label d-block">{{ __('customer_registration.customer_type') }} *</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="customer_type" id="type_person"
                                        value="individual" {{ old('customer_type', 'company') == 'individual' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type_person">{{ __('customer_registration.person') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="customer_type" id="type_company"
                                        value="company" {{ old('customer_type', 'company') == 'company' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type_company">{{ __('customer_registration.company') }}</label>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name_en" class="form-label" id="label-customer-name-en">{{ __('customer_registration.company_name') }} (EN) *</label>
                                    <input type="text" class="form-control @error('name_en') is-invalid @enderror"
                                        id="name_en" name="name_en" value="{{ old('name_en') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="name_ar" class="form-label" id="label-customer-name-ar">{{ __('messages.name_ar') }} *</label>
                                    <input type="text" class="form-control @error('name_ar') is-invalid @enderror"
                                        id="name_ar" name="name_ar" value="{{ old('name_ar') }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6" id="container-contact-person">
                                    <label for="contact_person"
                                        class="form-label">{{ __('customer_registration.contact_person') }} *</label>
                                    <input type="text" class="form-control @error('contact_person') is-invalid @enderror"
                                        id="contact_person" name="contact_person" value="{{ old('contact_person') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="group_id" class="form-label">{{ __('messages.customer_group') }}</label>
                                    <select class="form-select @error('group_id') is-invalid @enderror" id="group_id" name="group_id">
                                        <option value="">{{ __('messages.select_group') }}</option>
                                        @foreach($customerGroups as $group)
                                            <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                                {{ $group->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="salesman_id" class="form-label">{{ __('messages.salesman') }}</label>
                                    <select class="form-select @error('salesman_id') is-invalid @enderror" id="salesman_id" name="salesman_id">
                                        <option value="">{{ __('messages.select_salesman') }}</option>
                                        @foreach($salesmen as $salesman)
                                            <option value="{{ $salesman->id }}" {{ old('salesman_id') == $salesman->id ? 'selected' : '' }}>
                                                {{ $salesman->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('customer_registration.email') }} *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email') }}" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">{{ __('customer_registration.phone') }} *</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                        name="phone" value="{{ old('phone') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mobile" class="form-label">{{ __('customer_registration.mobile') }}</label>
                                    <input type="text" class="form-control @error('mobile') is-invalid @enderror"
                                        id="mobile" name="mobile" value="{{ old('mobile') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('customer_registration.address_info') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="address" class="form-label">{{ __('customer_registration.address') }} <span class="address-required">*</span></label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                    name="address" rows="3" required>{{ old('address') }}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="city" class="form-label">{{ __('customer_registration.city') }} *</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" id="city"
                                        name="city" value="{{ old('city') }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="region" class="form-label">{{ __('messages.region') }}</label>
                                    <input type="text" class="form-control @error('region') is-invalid @enderror" id="region"
                                        name="region" value="{{ old('region') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="country" class="form-label">{{ __('customer_registration.country') }}
                                        *</label>
                                    <input type="text" class="form-control @error('country') is-invalid @enderror"
                                        id="country" name="country" value="{{ old('country', 'Saudi Arabia') }}" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="postal_code"
                                    class="form-label">{{ __('customer_registration.postal_code') }}</label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                                    id="postal_code" name="postal_code" value="{{ old('postal_code') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('customer_registration.business_info') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tax_number"
                                        class="form-label">{{ __('customer_registration.tax_number') }} <span class="tax-required">*</span></label>
                                    <input type="text" class="form-control @error('tax_number') is-invalid @enderror"
                                        id="tax_number" name="tax_number" value="{{ old('tax_number') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="registration_number"
                                        class="form-label">{{ __('customer_registration.registration_number') }}</label>
                                    <input type="text"
                                        class="form-control @error('registration_number') is-invalid @enderror"
                                        id="registration_number" name="registration_number"
                                        value="{{ old('registration_number') }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="business_type"
                                    class="form-label">{{ __('customer_registration.business_type') }}</label>
                                <input type="text" class="form-control @error('business_type') is-invalid @enderror"
                                    id="business_type" name="business_type" value="{{ old('business_type') }}">
                            </div>
                            <div class="mb-3">
                                <label for="website" class="form-label">{{ __('customer_registration.website') }}</label>
                                <input type="url" class="form-control @error('website') is-invalid @enderror" id="website"
                                    name="website" value="{{ old('website') }}">
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('customer_registration.credit_info') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="credit_limit"
                                    class="form-label">{{ __('customer_registration.credit_limit') }}</label>
                                <input type="number" class="form-control @error('credit_limit') is-invalid @enderror"
                                    id="credit_limit" name="credit_limit" value="{{ old('credit_limit') }}" min="0"
                                    step="0.01">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="payment_terms"
                                        class="form-label">{{ __('customer_registration.payment_terms') }}</label>
                                    <select class="form-select @error('payment_terms') is-invalid @enderror"
                                        id="payment_terms" name="payment_terms">
                                        <option value="cash" {{ old('payment_terms') == 'cash' ? 'selected' : '' }}>{{ __('customer_registration.cash') }}</option>
                                        <option value="credit_15" {{ old('payment_terms') == 'credit_15' ? 'selected' : '' }}>{{ __('customer_registration.credit_15') }}</option>
                                        <option value="credit_30" {{ old('payment_terms', 'credit_30') == 'credit_30' ? 'selected' : '' }}>{{ __('customer_registration.credit_30') }}</option>
                                        <option value="credit_45" {{ old('payment_terms') == 'credit_45' ? 'selected' : '' }}>{{ __('customer_registration.credit_45') }}</option>
                                        <option value="credit_60" {{ old('payment_terms') == 'credit_60' ? 'selected' : '' }}>{{ __('customer_registration.credit_60') }}</option>
                                        <option value="credit_90" {{ old('payment_terms') == 'credit_90' ? 'selected' : '' }}>{{ __('customer_registration.credit_90') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="opening_balance" class="form-label">{{ __('messages.opening_balance') }}</label>
                                    <input type="number" step="0.01" class="form-control @error('opening_balance') is-invalid @enderror" 
                                        id="opening_balance" name="opening_balance" value="{{ old('opening_balance', 0) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('customer_registration.documents') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="documents"
                                    class="form-label">{{ __('customer_registration.upload_documents') }}</label>
                                <input type="file" class="form-control @error('documents') is-invalid @enderror"
                                    id="documents" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <small class="text-muted">{{ __('customer_registration.allowed_formats') }}</small>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">{{ __('customer_registration.notes') }}</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                                    rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-save me-1"></i> {{ __('general.save') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('turbo:load', function() {
        const typePerson = document.getElementById('type_person');
        const typeCompany = document.getElementById('type_company');
        const addressField = document.getElementById('address');
        const taxField = document.getElementById('tax_number');
        const addressRequiredSpan = document.querySelector('.address-required');
        const taxRequiredSpan = document.querySelector('.tax-required');
        const nameField = document.getElementById('name_en');

        const infoCardHeader = document.getElementById('info-card-header');
        const labelCustomerNameEn = document.getElementById('label-customer-name-en');
        const labelCustomerNameAr = document.getElementById('label-customer-name-ar');
        const containerContactPerson = document.getElementById('container-contact-person');
        const contactPersonInput = document.getElementById('contact_person');

        const trans = {
            company_info: "{{ __('customer_registration.company_info') }}",
            personal_info: "{{ __('customer_registration.personal_info') }}",
            company_name_en: "{{ __('customer_registration.company_name') }} (EN)",
            person_name_en: "{{ __('customer_registration.person_name') }} (EN)",
            company_name_ar: "{{ __('messages.name_ar') }}",
            person_name_ar: "{{ __('messages.name_ar') }}"
        };

        function toggleRequired() {
            if (typeCompany.checked) {
                addressField.required = true;
                taxField.required = true;
                addressRequiredSpan.style.display = 'inline';
                taxRequiredSpan.style.display = 'inline';

                // Specific UI for Company
                infoCardHeader.textContent = trans.company_info;
                labelCustomerNameEn.innerHTML = trans.company_name_en + ' *';
                labelCustomerNameAr.innerHTML = trans.company_name_ar + ' *';
                containerContactPerson.style.display = 'block';
                contactPersonInput.required = true;
            } else {
                addressField.required = false;
                taxField.required = false;
                addressRequiredSpan.style.display = 'none';
                taxRequiredSpan.style.display = 'none';

                // Specific UI for Person
                infoCardHeader.textContent = trans.personal_info;
                labelCustomerNameEn.innerHTML = trans.person_name_en + ' *';
                labelCustomerNameAr.innerHTML = trans.person_name_ar + ' *';
                containerContactPerson.style.display = 'none';
                contactPersonInput.required = false;
            }
        }

        typePerson.addEventListener('change', toggleRequired);
        typeCompany.addEventListener('change', toggleRequired);

        // Initial check
        toggleRequired();
    });
</script>
@endpush