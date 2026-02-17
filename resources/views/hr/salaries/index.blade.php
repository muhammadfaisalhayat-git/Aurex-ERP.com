@extends('layouts.app')

@section('title', __('messages.salary_management'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.salary_management') }}</h1>
            <div class="actions">
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print me-2"></i> {{ __('messages.print') }}
                </button>
            </div>
        </div>

        <div class="card glassy">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.employee') }}</th>
                                <th>{{ __('messages.department') }}</th>
                                <th class="text-end">{{ __('messages.basic_salary') }}</th>
                                <th class="text-end">{{ __('messages.allowances') }}</th>
                                <th class="text-end">{{ __('messages.total_salary') }}</th>
                                <th class="text-center">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
                                @php
                                    $allowances = ($employee->house_rent_allowance ?? 0) +
                                        ($employee->conveyance_allowance ?? 0) +
                                        ($employee->dearness_allowance ?? 0) +
                                        ($employee->overtime_allowance ?? 0) +
                                        ($employee->other_allowance ?? 0);
                                    $total = ($employee->basic_salary ?? 0) + $allowances;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3 text-primary">
                                                <i class="fas fa-user-circle fa-2x"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $employee->name }}</div>
                                                <small class="text-muted">{{ $employee->employee_code }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $employee->department->name ?? '-' }}</td>
                                    <td class="text-end">{{ number_format($employee->basic_salary ?? 0, 2) }}</td>
                                    <td class="text-end text-success">+{{ number_format($allowances, 2) }}</td>
                                    <td class="text-end fw-bold">{{ number_format($total, 2) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('hr.employees.salary-slip', $employee) }}"
                                            class="btn btn-sm btn-outline-primary" target="_blank"
                                            title="{{ __('messages.view_salary_slip') }}">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        {{ __('messages.no_records_found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($employees->count() > 0)
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="2" class="text-end">{{ __('messages.total') }}</td>
                                    <td class="text-end">{{ number_format($employees->sum('basic_salary'), 2) }}</td>
                                    <td class="text-end">
                                        @php
                                            $totalAllowances = $employees->sum(function ($e) {
                                                return ($e->house_rent_allowance ?? 0) +
                                                    ($e->conveyance_allowance ?? 0) +
                                                    ($e->dearness_allowance ?? 0) +
                                                    ($e->overtime_allowance ?? 0) +
                                                    ($e->other_allowance ?? 0);
                                            });
                                        @endphp
                                        {{ number_format($totalAllowances, 2) }}
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($employees->sum('basic_salary') + $totalAllowances, 2) }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection