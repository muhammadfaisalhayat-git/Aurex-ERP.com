@extends('layouts.app')

@section('title', __('messages.edit_employee'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.edit_employee') }}</h1>
            <a href="{{ route('hr.employees.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="card glassy">
            <div class="card-body">
                <form action="{{ route('hr.employees.update', $employee) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.employee_code') }}</label>
                            <input type="text" name="employee_code" class="form-control"
                                value="{{ $employee->employee_code }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.first_name_en') }}</label>
                            <input type="text" name="first_name_en" class="form-control"
                                value="{{ $employee->first_name_en }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.last_name_en') }}</label>
                            <input type="text" name="last_name_en" class="form-control"
                                value="{{ $employee->last_name_en }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.email') }}</label>
                            <input type="email" name="email" class="form-control" value="{{ $employee->email }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.department') }}</label>
                            <select name="department_id" class="form-select">
                                <option value="">{{ __('messages.select_department') }}</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ $employee->department_id == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.designation') }}</label>
                            <select name="designation_id" class="form-select">
                                <option value="">{{ __('messages.select_designation') }}</option>
                                @foreach($designations as $desig)
                                    <option value="{{ $desig->id }}" {{ $employee->designation_id == $desig->id ? 'selected' : '' }}>{{ $desig->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.joining_date') }}</label>
                            <input type="date" name="joining_date" class="form-control"
                                value="{{ $employee->joining_date->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.status') }}</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ $employee->status == 'active' ? 'selected' : '' }}>
                                    {{ __('messages.active') }}</option>
                                <option value="inactive" {{ $employee->status == 'inactive' ? 'selected' : '' }}>
                                    {{ __('messages.inactive') }}</option>
                                <option value="on_leave" {{ $employee->status == 'on_leave' ? 'selected' : '' }}>
                                    {{ __('messages.on_leave') }}</option>
                                <option value="terminated" {{ $employee->status == 'terminated' ? 'selected' : '' }}>
                                    {{ __('messages.terminated') }}</option>
                            </select>
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