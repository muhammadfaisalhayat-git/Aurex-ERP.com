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
                            <h5 class="mb-0">{{ __('customer_registration.company_info') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="company_name" class="form-label">{{ __('customer_registration.company_name') }}
                                    *</label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                    id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="contact_person"
                                    class="form-label">{{ __('customer_registration.contact_person') }} *</label>
                                <input type="text" class="form-control @error('contact_person') is-invalid @enderror"
                                    id="contact_person" name="contact_person" value="{{ old('contact_person') }}" required>
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
                                <label for="address" class="form-label">{{ __('customer_registration.address') }} *</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                    name="address" rows="3" required>{{ old('address') }}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">{{ __('customer_registration.city') }} *</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" id="city"
                                        name="city" value="{{ old('city') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
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
                                        class="form-label">{{ __('customer_registration.tax_number') }}</label>
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
                            <div class="mb-3">
                                <label for="payment_terms"
                                    class="form-label">{{ __('customer_registration.payment_terms') }}</label>
                                <input type="text" class="form-control @error('payment_terms') is-invalid @enderror"
                                    id="payment_terms" name="payment_terms" value="{{ old('payment_terms') }}"
                                    placeholder="e.g., Net 30 days">
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
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ __('general.save') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection