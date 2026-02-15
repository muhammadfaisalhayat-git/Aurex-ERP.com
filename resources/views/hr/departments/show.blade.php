@extends('layouts.app')

@section('title', $department->name)

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ $department->name }}</h1>
            <div>
                <a href="{{ route('hr.departments.edit', $department) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i> {{ __('messages.edit') }}
                </a>
                <a href="{{ route('hr.departments.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
                </a>
            </div>
        </div>

        <div class="card glassy mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ __('messages.basic_information') }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th style="width: 200px;">{{ __('messages.code') }}</th>
                        <td><code>{{ $department->code }}</code></td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.name_en') }}</th>
                        <td>{{ $department->name_en }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.name_ar') }}</th>
                        <td>{{ $department->name_ar ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.branch') }}</th>
                        <td>{{ $department->branch->name ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card glassy h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('messages.designations') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush bg-transparent">
                            @forelse($department->designations as $desig)
                                <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center">
                                    {{ $desig->name }}
                                    <span class="badge bg-primary rounded-pill">{{ $desig->employees_count ?? 0 }}</span>
                                </li>
                            @empty
                                <li class="list-group-item bg-transparent text-muted">{{ __('messages.no_records_found') }}</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card glassy h-100">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('messages.employees') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush bg-transparent">
                            @forelse($department->employees as $emp)
                                <li class="list-group-item bg-transparent">
                                    {{ $emp->name }}
                                    <small class="text-muted d-block">{{ $emp->designation->name ?? '-' }}</small>
                                </li>
                            @empty
                                <li class="list-group-item bg-transparent text-muted">{{ __('messages.no_records_found') }}</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection