@extends('layouts.app')

@section('title', __('supplier_registration.edit'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('supplier_registration.edit') }}</h1>
            <a href="{{ route('purchases.supplier-registrations.show', $supplierRegistration) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('general.back') }}
            </a>
        </div>

        <form action="{{ route('purchases.supplier-registrations.update', $supplierRegistration) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('supplier_registration.company_info') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="company_name" class="form-label">{{ __('supplier_registration.company_name') }}
                                    *</label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                    id="company_name" name="company_name"
                                    value="{{ old('company_name', $supplierRegistration->company_name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="contact_person"
                                    class="form-label">{{ __('supplier_registration.contact_person') }} *</label>
                                <input type="text" class="form-control @error('contact_person') is-invalid @enderror"
                                    id="contact_person" name="contact_person"
                                    value="{{ old('contact_person', $supplierRegistration->contact_person) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('supplier_registration.email') }} *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email', $supplierRegistration->email) }}" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">{{ __('supplier_registration.phone') }} *</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                        name="phone" value="{{ old('phone', $supplierRegistration->phone) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mobile" class="form-label">{{ __('supplier_registration.mobile') }}</label>
                                    <input type="text" class="form-control @error('mobile') is-invalid @enderror"
                                        id="mobile" name="mobile"
                                        value="{{ old('mobile', $supplierRegistration->mobile) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('supplier_registration.address_info') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="address" class="form-label">{{ __('supplier_registration.address') }} *</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                    name="address" rows="3"
                                    required>{{ old('address', $supplierRegistration->address) }}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">{{ __('supplier_registration.city') }} *</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" id="city"
                                        name="city" value="{{ old('city', $supplierRegistration->city) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="country" class="form-label">{{ __('supplier_registration.country') }}
                                        *</label>
                                    <input type="text" class="form-control @error('country') is-invalid @enderror"
                                        id="country" name="country"
                                        value="{{ old('country', $supplierRegistration->country) }}" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="postal_code"
                                    class="form-label">{{ __('supplier_registration.postal_code') }}</label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                                    id="postal_code" name="postal_code"
                                    value="{{ old('postal_code', $supplierRegistration->postal_code) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('supplier_registration.business_info') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tax_number"
                                        class="form-label">{{ __('supplier_registration.tax_number') }}</label>
                                    <input type="text" class="form-control @error('tax_number') is-invalid @enderror"
                                        id="tax_number" name="tax_number"
                                        value="{{ old('tax_number', $supplierRegistration->tax_number) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="registration_number"
                                        class="form-label">{{ __('supplier_registration.registration_number') }}</label>
                                    <input type="text"
                                        class="form-control @error('registration_number') is-invalid @enderror"
                                        id="registration_number" name="registration_number"
                                        value="{{ old('registration_number', $supplierRegistration->registration_number) }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="business_type"
                                    class="form-label">{{ __('supplier_registration.business_type') }}</label>
                                <input type="text" class="form-control @error('business_type') is-invalid @enderror"
                                    id="business_type" name="business_type"
                                    value="{{ old('business_type', $supplierRegistration->business_type) }}">
                            </div>
                            <div class="mb-3">
                                <label for="website" class="form-label">{{ __('supplier_registration.website') }}</label>
                                <input type="url" class="form-control @error('website') is-invalid @enderror" id="website"
                                    name="website" value="{{ old('website', $supplierRegistration->website) }}">
                            </div>
                            <div class="mb-3">
                                <label for="products_services"
                                    class="form-label">{{ __('supplier_registration.products_services') }}</label>
                                <textarea class="form-control @error('products_services') is-invalid @enderror"
                                    id="products_services" name="products_services"
                                    rows="3">{{ old('products_services', $supplierRegistration->products_services) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('supplier_registration.bank_info') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="bank_name"
                                    class="form-label">{{ __('supplier_registration.bank_name') }}</label>
                                <input type="text" class="form-control @error('bank_name') is-invalid @enderror"
                                    id="bank_name" name="bank_name"
                                    value="{{ old('bank_name', $supplierRegistration->bank_name) }}">
                            </div>
                            <div class="mb-3">
                                <label for="bank_account"
                                    class="form-label">{{ __('supplier_registration.bank_account') }}</label>
                                <input type="text" class="form-control @error('bank_account') is-invalid @enderror"
                                    id="bank_account" name="bank_account"
                                    value="{{ old('bank_account', $supplierRegistration->bank_account) }}">
                            </div>
                            <div class="mb-3">
                                <label for="iban" class="form-label">{{ __('supplier_registration.iban') }}</label>
                                <input type="text" class="form-control @error('iban') is-invalid @enderror" id="iban"
                                    name="iban" value="{{ old('iban', $supplierRegistration->iban) }}">
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('supplier_registration.documents') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="documents"
                                    class="form-label">{{ __('supplier_registration.upload_documents') }}</label>
                                <input type="file" class="form-control @error('documents') is-invalid @enderror"
                                    id="documents" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <small class="text-muted">{{ __('supplier_registration.allowed_formats') }}</small>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">{{ __('supplier_registration.notes') }}</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                                    rows="3">{{ old('notes', $supplierRegistration->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ __('general.update') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection