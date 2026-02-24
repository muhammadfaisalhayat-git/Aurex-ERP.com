@extends('layouts.app')

@section('title', __('messages.employees'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.employees') }}</h1>
            <a href="{{ route('hr.employees.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> {{ __('messages.create') }}
            </a>
        </div>

        
            <div class="card glassy">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.code') }}</th>
                                    <th>{{ __('messages.name') }}</th>
                                    <th>{{ __('messages.department') }}</th>
                                    <th>{{ __('messages.designation') }}</th>
                                    <th>{{ __('messages.joining_date') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $employee)
                                    <tr>
                                        <td><code>{{ $employee->employee_code }}</code></td>
                                        <td>{{ $employee->name }}</td>
                                        <td>{{ $employee->department->name ?? '-' }}</td>
                                        <td>{{ $employee->designation->name ?? '-' }}</td>
                                        <td>{{ $employee->joining_date->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $employee->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ __('messages.' . $employee->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('hr.employees.show', $employee) }}"
                                                    class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('hr.employees.edit', $employee) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            {{ __('messages.no_records_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $employees->links() }}
                    </div>
                </div>
            </div>
        
    </div>
@endsection