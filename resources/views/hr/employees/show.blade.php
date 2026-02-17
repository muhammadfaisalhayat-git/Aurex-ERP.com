@extends('layouts.app')

@section('title', $employee->name)

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ $employee->name }}</h1>
            <div>
                <a href="{{ route('hr.employees.salary-slip', $employee) }}" class="btn btn-outline-danger me-2"
                    target="_blank">
                    <i class="fas fa-file-pdf me-2"></i> {{ __('messages.salary_slip') }}
                </a>
                <a href="{{ route('hr.employees.edit', $employee) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i> {{ __('messages.edit') }}
                </a>
                <a href="{{ route('hr.employees.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card glassy h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-user-circle fa-5x text-muted"></i>
                        </div>
                        <h4 class="mb-0">{{ $employee->name }}</h4>
                        @if($employee->first_name_ar || $employee->last_name_ar)
                            <p class="text-muted mb-0">{{ $employee->first_name_ar }} {{ $employee->last_name_ar }}</p>
                        @endif
                        <p class="text-muted">{{ $employee->designation->name ?? '-' }}</p>
                        <hr>
                        <div class="text-start">
                            <p><strong>{{ __('messages.employee_code') }}:</strong>
                                <code>{{ $employee->employee_code }}</code>
                            </p>
                            <p><strong>{{ __('messages.status') }}:</strong> <span
                                    class="badge bg-success">{{ __('messages.active') }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card glassy h-100">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('messages.basic_information') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 200px;">{{ __('messages.first_name_ar') }}</th>
                                <td>{{ $employee->first_name_ar ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.last_name_ar') }}</th>
                                <td>{{ $employee->last_name_ar ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.email') }}</th>
                                <td>{{ $employee->email }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.phone') }}</th>
                                <td>{{ $employee->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.nationality') }}</th>
                                <td>{{ $employee->nationality ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.department') }}</th>
                                <td>{{ $employee->department->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.designation') }}</th>
                                <td>{{ $employee->designation->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.joining_date') }}</th>
                                <td>{{ $employee->joining_date->format('Y-m-d') }}</td>
                            </tr>
                        </table>

                        <h5 class="mt-4 mb-3">{{ __('messages.salary_information') }}</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 200px;">{{ __('messages.basic_salary') }}</th>
                                <td>{{ number_format($employee->basic_salary, 2) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.house_rent_allowance') }}</th>
                                <td>{{ number_format($employee->house_rent_allowance, 2) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.conveyance_allowance') }}</th>
                                <td>{{ number_format($employee->conveyance_allowance, 2) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.dearness_allowance') }}</th>
                                <td>{{ number_format($employee->dearness_allowance, 2) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.overtime_allowance') }}</th>
                                <td>{{ number_format($employee->overtime_allowance, 2) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.other_allowance') }}</th>
                                <td>{{ number_format($employee->other_allowance, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection