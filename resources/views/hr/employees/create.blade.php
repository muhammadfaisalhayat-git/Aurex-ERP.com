@extends('layouts.app')

@section('title', __('messages.create_employee'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.create_employee') }}</h1>
            <a href="{{ route('hr.employees.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card glassy">
            <div class="card-body">
                <form action="{{ route('hr.employees.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="active">
                    <div class="row g-3">
                        <!-- Row 1: Identity -->
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.employee_code') }}</label>
                            <input type="text" name="employee_code"
                                class="form-control @error('employee_code') is-invalid @enderror"
                                value="{{ old('employee_code') }}" required>
                            @error('employee_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.email') }}</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.phone') }}</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.nationality') }}</label>
                            <input type="text" name="nationality"
                                class="form-control @error('nationality') is-invalid @enderror"
                                value="{{ old('nationality') }}">
                            @error('nationality')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Row 2: Names -->
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.first_name_en') }}</label>
                            <input type="text" name="first_name_en"
                                class="form-control @error('first_name_en') is-invalid @enderror"
                                value="{{ old('first_name_en') }}" required>
                            @error('first_name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.last_name_en') }}</label>
                            <input type="text" name="last_name_en"
                                class="form-control @error('last_name_en') is-invalid @enderror"
                                value="{{ old('last_name_en') }}" required>
                            @error('last_name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.first_name_ar') }}</label>
                            <input type="text" name="first_name_ar"
                                class="form-control @error('first_name_ar') is-invalid @enderror"
                                value="{{ old('first_name_ar') }}">
                            @error('first_name_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.last_name_ar') }}</label>
                            <input type="text" name="last_name_ar"
                                class="form-control @error('last_name_ar') is-invalid @enderror"
                                value="{{ old('last_name_ar') }}">
                            @error('last_name_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Row 3: Employment -->
                        <div class="col-md-3 text-force-left">
                            <label class="form-label">{{ __('messages.department') }}</label>
                            <select name="department_id" class="form-select @error('department_id') is-invalid @enderror">
                                <option value="">{{ __('messages.select_department') }}</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 text-force-left">
                            <label class="form-label">{{ __('messages.designation') }}</label>
                            <select name="designation_id" class="form-select @error('designation_id') is-invalid @enderror">
                                <option value="">{{ __('messages.select_designation') }}</option>
                                @foreach($designations as $desig)
                                    <option value="{{ $desig->id }}" {{ old('designation_id') == $desig->id ? 'selected' : '' }}>
                                        {{ $desig->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('designation_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 text-force-left">
                            <label class="form-label">{{ __('messages.joining_date') }}</label>
                            <input type="date" name="joining_date"
                                class="form-control @error('joining_date') is-invalid @enderror"
                                value="{{ old('joining_date') }}" required>
                            @error('joining_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 text-force-left">
                            <label class="form-label">{{ __('messages.status') }}</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                    {{ __('messages.active') }}
                                </option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                    {{ __('messages.inactive') }}
                                </option>
                                <option value="on_leave" {{ old('status') == 'on_leave' ? 'selected' : '' }}>
                                    {{ __('messages.on_leave') }}
                                </option>
                                <option value="terminated" {{ old('status') == 'terminated' ? 'selected' : '' }}>
                                    {{ __('messages.terminated') }}
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Row 4: Salary components -->
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.basic_salary') }}</label>
                            <input type="number" step="0.01" name="basic_salary"
                                class="form-control @error('basic_salary') is-invalid @enderror"
                                value="{{ old('basic_salary', 0) }}">
                            @error('basic_salary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.house_rent_allowance') }}</label>
                            <input type="number" step="0.01" name="house_rent_allowance"
                                class="form-control @error('house_rent_allowance') is-invalid @enderror"
                                value="{{ old('house_rent_allowance', 0) }}">
                            @error('house_rent_allowance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.conveyance_allowance') }}</label>
                            <input type="number" step="0.01" name="conveyance_allowance"
                                class="form-control @error('conveyance_allowance') is-invalid @enderror"
                                value="{{ old('conveyance_allowance', 0) }}">
                            @error('conveyance_allowance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.dearness_allowance') }}</label>
                            <input type="number" step="0.01" name="dearness_allowance"
                                class="form-control @error('dearness_allowance') is-invalid @enderror"
                                value="{{ old('dearness_allowance', 0) }}">
                            @error('dearness_allowance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Row 5 -->
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.overtime_allowance') }}</label>
                            <input type="number" step="0.01" name="overtime_allowance"
                                class="form-control @error('overtime_allowance') is-invalid @enderror"
                                value="{{ old('overtime_allowance', 0) }}">
                            @error('overtime_allowance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.other_allowance') }}</label>
                            <input type="number" step="0.01" name="other_allowance"
                                class="form-control @error('other_allowance') is-invalid @enderror"
                                value="{{ old('other_allowance', 0) }}">
                            @error('other_allowance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> {{ __('messages.save') }}
                </button>
            </div>
            </form>
        </div>
    </div>
    </div>
@endsection