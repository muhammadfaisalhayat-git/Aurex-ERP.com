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

        <div class="card glassy">
            <div class="card-body">
                <form action="{{ route('hr.employees.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.employee_code') }}</label>
                            <input type="text" name="employee_code" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.first_name_en') }}</label>
                            <input type="text" name="first_name_en" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.last_name_en') }}</label>
                            <input type="text" name="last_name_en" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('messages.email') }}</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.department') }}</label>
                            <select name="department_id" class="form-select">
                                <option value="">{{ __('messages.select_department') }}</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.designation') }}</label>
                            <select name="designation_id" class="form-select">
                                <option value="">{{ __('messages.select_designation') }}</option>
                                @foreach($designations as $desig)
                                    <option value="{{ $desig->id }}">{{ $desig->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.joining_date') }}</label>
                            <input type="date" name="joining_date" class="form-control" required>
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