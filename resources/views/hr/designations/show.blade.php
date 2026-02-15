@extends('layouts.app')

@section('title', $designation->name)

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ $designation->name }}</h1>
            <div>
                <a href="{{ route('hr.designations.edit', $designation) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i> {{ __('messages.edit') }}
                </a>
                <a href="{{ route('hr.designations.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-5">
                <div class="card glassy h-100">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('messages.basic_information') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 150px;">{{ __('messages.code') }}</th>
                                <td><code>{{ $designation->code }}</code></td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.name_en') }}</th>
                                <td>{{ $designation->name_en }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.name_ar') }}</th>
                                <td>{{ $designation->name_ar ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.department') }}</th>
                                <td>{{ $designation->department->name ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card glassy h-100">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('messages.employees') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush bg-transparent">
                            @forelse($designation->employees as $emp)
                                <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center">
                                    {{ $emp->name }}
                                    <small class="text-muted">{{ $emp->employee_code }}</small>
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