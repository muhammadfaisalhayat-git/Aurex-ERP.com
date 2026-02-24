@extends('layouts.app')

@section('title', __('messages.create_vendor'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.create_vendor') }}</h1>
            <a href="{{ route('purchases.vendors.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('purchases.vendors.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-12 mb-3">
                            <h5 class="border-bottom pb-2">{{ __('messages.basic_information') }}</h5>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="type" class="form-label">{{ __('messages.vendor_type') }} <span
                                    class="text-danger">*</span></label>
                            <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="vendor" {{ old('type') == 'vendor' ? 'selected' : '' }}>
                                    {{ __('messages.vendor') }}
                                </option>
                                <option value="local_supplier" {{ old('type') == 'local_supplier' ? 'selected' : '' }}>
                                    {{ __('messages.local_supplier') }}
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="code" class="form-label">{{ __('messages.code') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code"
                                name="code" value="{{ old('code', $nextCode) }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="name_en" class="form-label">{{ __('messages.name_en') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name_en') is-invalid @enderror" id="name_en"
                                name="name_en" value="{{ old('name_en') }}" required>
                            @error('name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="name_ar" class="form-label">{{ __('messages.name_ar') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name_ar') is-invalid @enderror" id="name_ar"
                                name="name_ar" value="{{ old('name_ar') }}" required>
                            @error('name_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="branch_id" class="form-label">{{ __('messages.branch') }}</label>
                            <select class="form-control @error('branch_id') is-invalid @enderror" id="branch_id"
                                name="branch_id">
                                <option value="">{{ __('messages.select_branch') }}</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
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
                                id="contact_person" name="contact_person" value="{{ old('contact_person') }}">
                            @error('contact_person')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="phone" class="form-label">{{ __('messages.phone') }}</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="mobile" class="form-label">{{ __('messages.mobile') }}</label>
                            <input type="text" class="form-control @error('mobile') is-invalid @enderror" id="mobile"
                                name="mobile" value="{{ old('mobile') }}">
                            @error('mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="whatsapp_number" class="form-label">
                                {{ __('messages.whatsapp_number') }} <span class="text-danger asterisk">*</span>
                            </label>
                            <input type="text" class="form-control @error('whatsapp_number') is-invalid @enderror"
                                id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number') }}" required>
                            @error('whatsapp_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="email" class="form-label">{{ __('messages.email') }}</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address Information -->
                        <div class="col-12 mb-3 mt-3">
                            <h5 class="border-bottom pb-2">{{ __('messages.address_information') }}</h5>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="address" class="form-label">
                                {{ __('messages.address') }} <span class="text-danger asterisk"
                                    id="address-asterisk">*</span>
                            </label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address"
                                name="address" value="{{ old('address') }}">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="city" class="form-label">{{ __('messages.city') }}</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" id="city"
                                name="city" value="{{ old('city') }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="region" class="form-label">{{ __('messages.region') }}</label>
                            <input type="text" class="form-control @error('region') is-invalid @enderror" id="region"
                                name="region" value="{{ old('region') }}">
                            @error('region')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="postal_code" class="form-label">{{ __('messages.postal_code') }}</label>
                            <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                                id="postal_code" name="postal_code" value="{{ old('postal_code') }}">
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Financial Information -->
                        <div class="col-12 mb-3 mt-3">
                            <h5 class="border-bottom pb-2">{{ __('messages.financial_information') }}</h5>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="tax_number" class="form-label">
                                {{ __('messages.tax_number') }} <span class="text-danger asterisk"
                                    id="tax-asterisk">*</span>
                            </label>
                            <input type="text" class="form-control @error('tax_number') is-invalid @enderror"
                                id="tax_number" name="tax_number" value="{{ old('tax_number') }}">
                            @error('tax_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="commercial_registration"
                                class="form-label">{{ __('messages.commercial_registration') }}</label>
                            <input type="text" class="form-control @error('commercial_registration') is-invalid @enderror"
                                id="commercial_registration" name="commercial_registration"
                                value="{{ old('commercial_registration') }}">
                            @error('commercial_registration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="opening_balance" class="form-label">{{ __('messages.opening_balance') }}</label>
                            <input type="number" step="0.01"
                                class="form-control @error('opening_balance') is-invalid @enderror" id="opening_balance"
                                name="opening_balance" value="{{ old('opening_balance', 0) }}">
                            @error('opening_balance')
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
                                rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">{{ __('messages.status') }} <span
                                    class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status"
                                required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                    {{ __('messages.active') }}
                                </option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                    {{ __('messages.inactive') }}
                                </option>
                                <option value="blocked" {{ old('status') == 'blocked' ? 'selected' : '' }}>
                                    {{ __('messages.blocked') }}
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('messages.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function() {
                function updateMandatoryFields() {
                    const type = $('#type').val();
                    if (type === 'vendor') {
                        $('#tax-asterisk').show();
                        $('#tax_number').prop('required', true);
                        $('#address-asterisk').show();
                        $('#address').prop('required', true);
                    } else {
                        $('#tax-asterisk').hide();
                        $('#tax_number').prop('required', false);
                        $('#address-asterisk').hide();
                        $('#address').prop('required', false);
                    }
                }

                $('#type').on('change', function() {
                    updateMandatoryFields();

                    const type = $(this).val();
                    $.ajax({
                        url: "{{ route('ajax.vendors.next-code') }}",
                        data: {
                            type: type
                        },
                        success: function(response) {
                            $('#code').val(response.code);
                        }
                    });
                });

                updateMandatoryFields();
            });
        </script>
    @endpush
@endsection