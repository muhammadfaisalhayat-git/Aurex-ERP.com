@extends('layouts.app')

@section('title', __('messages.transport') . ' - ' . __('messages.add_contract'))

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('messages.add_contract') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('transport.contracts.index') }}">{{ __('messages.transport_contracts') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.add_new') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('transport.contracts.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('messages.contract_number') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="contract_number"
                                    class="form-control @error('contract_number') is-invalid @enderror"
                                    value="{{ old('contract_number') }}" required>
                                @error('contract_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">{{ __('messages.contract_date') }} <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="contract_date"
                                    class="form-control @error('contract_date') is-invalid @enderror"
                                    value="{{ old('contract_date', date('Y-m-d')) }}" required>
                                @error('contract_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">{{ __('messages.contract_value') }} <span
                                        class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="contract_value"
                                    class="form-control @error('contract_value') is-invalid @enderror"
                                    value="{{ old('contract_value') }}" required>
                                @error('contract_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('messages.start_date') }} <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="start_date"
                                    class="form-control @error('start_date') is-invalid @enderror"
                                    value="{{ old('start_date') }}" required>
                                @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('messages.end_date') }} <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="end_date"
                                    class="form-control @error('end_date') is-invalid @enderror"
                                    value="{{ old('end_date') }}" required>
                                @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('messages.contractor_name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="contractor_name"
                                    class="form-control @error('contractor_name') is-invalid @enderror"
                                    value="{{ old('contractor_name') }}" required>
                                @error('contractor_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('messages.contractor_phone') }}</label>
                                <input type="text" name="contractor_phone"
                                    class="form-control @error('contractor_phone') is-invalid @enderror"
                                    value="{{ old('contractor_phone') }}">
                                @error('contractor_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('messages.status') }} <span
                                        class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>
                                        {{ __('messages.pending') }}</option>
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                        {{ __('messages.active') }}</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 text-end mt-4">
                                <a href="{{ route('transport.contracts.index') }}"
                                    class="btn btn-outline-secondary px-4 me-2">{{ __('messages.cancel') }}</a>
                                <button type="submit" class="btn btn-primary px-5">
                                    <i class="fas fa-save me-2"></i>{{ __('messages.save_contract') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection