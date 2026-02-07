@extends('layouts.app')

@section('title', __('messages.edit_customer'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.edit_customer') }}: {{ $customer->name }}</h1>
            <a href="{{ route('sales.customers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('sales.customers.update', $customer) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-12 mb-3">
                            <h5 class="border-bottom pb-2">{{ __('messages.basic_information') }}</h5>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="code" class="form-label">{{ __('messages.code') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code"
                                name="code" value="{{ old('code', $customer->code) }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="name_en" class="form-label">{{ __('messages.name_en') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name_en') is-invalid @enderror" id="name_en"
                                name="name_en" value="{{ old('name_en', $customer->name_en) }}" required>
                            @error('name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="name_ar" class="form-label">{{ __('messages.name_ar') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name_ar') is-invalid @enderror" id="name_ar"
                                name="name_ar" value="{{ old('name_ar', $customer->name_ar) }}" required>
                            @error('name_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="group_id" class="form-label">{{ __('messages.customer_group') }}</label>
                            <select class="form-control @error('group_id') is-invalid @enderror" id="group_id"
                                name="group_id">
                                <option value="">{{ __('messages.select_group') }}</option>
                                @foreach($customerGroups as $group)
                                    <option value="{{ $group->id }}" {{ old('group_id', $customer->group_id) == $group->id ? 'selected' : '' }}>
                                        {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('group_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="branch_id" class="form-label">{{ __('messages.branch') }}</label>
                            <select class="form-control @error('branch_id') is-invalid @enderror" id="branch_id"
                                name="branch_id">
                                <option value="">{{ __('messages.select_branch') }}</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id', $customer->branch_id) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="salesman_id" class="form-label">{{ __('messages.salesman') }}</label>
                            <select class="form-control @error('salesman_id') is-invalid @enderror" id="salesman_id"
                                name="salesman_id">
                                <option value="">{{ __('messages.select_salesman') }}</option>
                                @foreach($salesmen as $salesman)
                                    <option value="{{ $salesman->id }}" {{ old('salesman_id', $customer->salesman_id) == $salesman->id ? 'selected' : '' }}>
                                        {{ $salesman->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('salesman_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Contact Information -->
                        <div class="col-12 mb-3 mt-3">
                            <h5 class="border-bottom pb-2">{{ __('messages.contact_information') }}</h5>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="contact_person" class="form-label">{{ __('messages.contact_person') }}</label>
                            <input type="text" class="form-control @error('contact_person') is-invalid @enderror"
                                id="contact_person" name="contact_person"
                                value="{{ old('contact_person', $customer->contact_person) }}">
                            @error('contact_person')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="phone" class="form-label">{{ __('messages.phone') }}</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                name="phone" value="{{ old('phone', $customer->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="mobile" class="form-label">{{ __('messages.mobile') }}</label>
                            <input type="text" class="form-control @error('mobile') is-invalid @enderror" id="mobile"
                                name="mobile" value="{{ old('mobile', $customer->mobile) }}">
                            @error('mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="email" class="form-label">{{ __('messages.email') }}</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', $customer->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address Information -->
                        <div class="col-12 mb-3 mt-3">
                            <h5 class="border-bottom pb-2">{{ __('messages.address_information') }}</h5>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="address" class="form-label">{{ __('messages.address') }}</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address"
                                name="address" value="{{ old('address', $customer->address) }}">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="city" class="form-label">{{ __('messages.city') }}</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" id="city"
                                name="city" value="{{ old('city', $customer->city) }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="region" class="form-label">{{ __('messages.region') }}</label>
                            <input type="text" class="form-control @error('region') is-invalid @enderror" id="region"
                                name="region" value="{{ old('region', $customer->region) }}">
                            @error('region')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="postal_code" class="form-label">{{ __('messages.postal_code') }}</label>
                            <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                                id="postal_code" name="postal_code"
                                value="{{ old('postal_code', $customer->postal_code) }}">
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Financial Information -->
                        <div class="col-12 mb-3 mt-3">
                            <h5 class="border-bottom pb-2">{{ __('messages.financial_information') }}</h5>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="tax_number" class="form-label">{{ __('messages.tax_number') }}</label>
                            <input type="text" class="form-control @error('tax_number') is-invalid @enderror"
                                id="tax_number" name="tax_number" value="{{ old('tax_number', $customer->tax_number) }}">
                            @error('tax_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="commercial_registration"
                                class="form-label">{{ __('messages.commercial_registration') }}</label>
                            <input type="text" class="form-control @error('commercial_registration') is-invalid @enderror"
                                id="commercial_registration" name="commercial_registration"
                                value="{{ old('commercial_registration', $customer->commercial_registration) }}">
                            @error('commercial_registration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="credit_limit" class="form-label">{{ __('messages.credit_limit') }}</label>
                            <input type="number" step="0.01"
                                class="form-control @error('credit_limit') is-invalid @enderror" id="credit_limit"
                                name="credit_limit" value="{{ old('credit_limit', $customer->credit_limit) }}">
                            @error('credit_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="credit_days" class="form-label">{{ __('messages.credit_days') }}</label>
                            <input type="number" class="form-control @error('credit_days') is-invalid @enderror"
                                id="credit_days" name="credit_days"
                                value="{{ old('credit_days', $customer->credit_days) }}">
                            @error('credit_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Other Information -->
                        <div class="col-12 mb-3 mt-3">
                            <h5 class="border-bottom pb-2">{{ __('messages.other_information') }}</h5>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="notes" class="form-label">{{ __('messages.notes') }}</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                                rows="3">{{ old('notes', $customer->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">{{ __('messages.status') }} <span
                                    class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status"
                                required>
                                <option value="active" {{ old('status', $customer->status) == 'active' ? 'selected' : '' }}>
                                    {{ __('messages.active') }}</option>
                                <option value="inactive" {{ old('status', $customer->status) == 'inactive' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                                <option value="blocked" {{ old('status', $customer->status) == 'blocked' ? 'selected' : '' }}>
                                    {{ __('messages.blocked') }}</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('messages.update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection