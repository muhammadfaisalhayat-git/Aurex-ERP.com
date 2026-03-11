@extends('layouts.app')

@section('title', __('crm.edit_opportunity'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('crm.edit_opportunity') }}: {{ $opportunity->title }}</h1>
            <a href="{{ route('crm.opportunities.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('crm.back_to_opportunities') }}
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('crm.opportunities.update', $opportunity) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3 text-primary">{{ __('crm.opportunity_info') }}</h5>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-bold">{{ __('crm.opportunity_title') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $opportunity->title) }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('crm.pipeline_stage') }} <span
                                    class="text-danger">*</span></label>
                            <select name="stage_id" class="form-select select2" required>
                                @foreach($stages as $stage)
                                    <option value="{{ $stage->id }}" {{ old('stage_id', $opportunity->stage_id) == $stage->id ? 'selected' : '' }}>
                                        {{ $stage->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('crm.linked_lead') }}</label>
                            <select name="lead_id" class="form-select select2">
                                <option value="">{{ __('common.none') }}</option>
                                @foreach($leads as $lead)
                                    <option value="{{ $lead->id }}" {{ old('lead_id', $opportunity->lead_id) == $lead->id ? 'selected' : '' }}>
                                        {{ $lead->full_name }} ({{ $lead->company_name ?? __('crm.individual') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('crm.linked_customer') }}</label>
                            <select name="customer_id" class="form-select select2">
                                <option value="">{{ __('common.none') }}</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id', $opportunity->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mt-5">
                            <h5 class="border-bottom pb-2 mb-3 text-primary">{{ __('crm.forecast_assignment') }}</h5>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('crm.expected_revenue') }} <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">{{ config('app.currency', 'SAR') }}</span>
                                <input type="number" step="0.01" name="expected_revenue"
                                    class="form-control @error('expected_revenue') is-invalid @enderror"
                                    value="{{ old('expected_revenue', $opportunity->expected_revenue) }}" required>
                            </div>
                            @error('expected_revenue') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('crm.probability') }} (%) <span
                                    class="text-danger">*</span></label>
                            <input type="number" name="probability"
                                class="form-control @error('probability') is-invalid @enderror"
                                value="{{ old('probability', $opportunity->probability) }}" required min="0" max="100">
                            @error('probability') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('crm.expected_closing') }}</label>
                            <input type="date" name="expected_closing" class="form-control"
                                value="{{ old('expected_closing', $opportunity->expected_closing ? $opportunity->expected_closing->format('Y-m-d') : '') }}">
                        </div>

                        <div class="col-md-6 mt-4">
                            <label class="form-label fw-bold">{{ __('crm.salesman') }}</label>
                            <select name="salesman_id" class="form-select select2">
                                <option value="">{{ __('common.select') }}</option>
                                @foreach($salesmen as $salesman)
                                    <option value="{{ $salesman->id }}" {{ old('salesman_id', $opportunity->salesman_id) == $salesman->id ? 'selected' : '' }}>
                                        {{ $salesman->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mt-4">
                            <label class="form-label fw-bold">{{ __('crm.branch') }}</label>
                            <select name="branch_id" class="form-select select2">
                                <option value="">{{ __('common.select') }}</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id', $opportunity->branch_id) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mt-4">
                            <label class="form-label fw-bold">{{ __('crm.description') }}</label>
                            <textarea name="description" class="form-control"
                                rows="4">{{ old('description', $opportunity->description) }}</textarea>
                        </div>

                        <div class="col-12 text-end mt-5">
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                                <i class="fas fa-save me-2"></i> {{ __('crm.update_opportunity') }}
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