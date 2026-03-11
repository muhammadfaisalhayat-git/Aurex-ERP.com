@extends('layouts.app')

@section('title', __('crm.edit_lead'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('crm.edit_lead') }}: {{ $lead->full_name }}</h1>
            <a href="{{ route('crm.leads.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('crm.back_to_leads') }}
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('crm.leads.update', $lead) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <!-- Basic Information -->
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3 text-primary">{{ __('crm.basic_information') }}</h5>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('crm.first_name') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="first_name"
                                class="form-control @error('first_name') is-invalid @enderror"
                                value="{{ old('first_name', $lead->first_name) }}" required>
                            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('crm.last_name') }}</label>
                            <input type="text" name="last_name"
                                class="form-control @error('last_name') is-invalid @enderror"
                                value="{{ old('last_name', $lead->last_name) }}">
                            @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('crm.company_name') }}</label>
                            <input type="text" name="company_name" class="form-control"
                                value="{{ old('company_name', $lead->company_name) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('crm.source') }}</label>
                            <select name="source" class="form-select">
                                <option value="">{{ __('common.select') }}</option>
                                @foreach(['website', 'email', 'phone', 'referral', 'direct'] as $source)
                                    <option value="{{ $source }}" {{ old('source', $lead->source) == $source ? 'selected' : '' }}>
                                        {{ __('crm.source_' . $source) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Contact Details -->
                        <div class="col-12 mt-5">
                            <h5 class="border-bottom pb-2 mb-3 text-primary">{{ __('crm.contact_details') }}</h5>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('crm.email') }}</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $lead->email) }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('crm.phone') }}</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $lead->phone) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('crm.mobile') }}</label>
                            <input type="text" name="mobile" class="form-control"
                                value="{{ old('mobile', $lead->mobile) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">{{ __('crm.address') }}</label>
                            <textarea name="address" class="form-control"
                                rows="2">{{ old('address', $lead->address) }}</textarea>
                        </div>

                        <!-- Assignment & Status -->
                        <div class="col-12 mt-5">
                            <h5 class="border-bottom pb-2 mb-3 text-primary">{{ __('crm.assignment_status') }}</h5>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('crm.salesman') }}</label>
                            <select name="salesman_id" class="form-select select2">
                                <option value="">{{ __('common.select') }}</option>
                                @foreach($salesmen as $salesman)
                                    <option value="{{ $salesman->id }}" {{ old('salesman_id', $lead->salesman_id) == $salesman->id ? 'selected' : '' }}>
                                        {{ $salesman->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('crm.branch') }}</label>
                            <select name="branch_id" class="form-select select2">
                                <option value="">{{ __('common.select') }}</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id', $lead->branch_id) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('crm.status') }}</label>
                            <select name="status" class="form-select" required>
                                @foreach(['new', 'contacted', 'qualified', 'converted', 'lost'] as $status)
                                    <option value="{{ $status }}" {{ old('status', $lead->status) == $status ? 'selected' : '' }}>
                                        {{ __('crm.status_' . $status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mt-4">
                            <label class="form-label fw-bold">{{ __('crm.notes') }}</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $lead->notes) }}</textarea>
                        </div>

                        <div class="col-12 text-end mt-5">
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                                <i class="fas fa-save me-2"></i> {{ __('crm.update_lead') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        });
    </script>
@endpush