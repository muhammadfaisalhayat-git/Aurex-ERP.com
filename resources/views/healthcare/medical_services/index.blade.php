@extends('layouts.app')

@section('title', __('messages.service_list') . ' - ' . __('messages.healthcare_management'))

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.medical_services') }}</h1>
        <a href="{{ route('healthcare.medical-services.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> {{ __('messages.add_service') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.service_list') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.code') }}</th>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.service_cost') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $service)
                            <tr>
                                <td><code>{{ $service->code }}</code></td>
                                <td>{{ App::getLocale() == 'ar' ? ($service->name_ar ?? $service->name_en) : $service->name_en }}</td>
                                <td class="text-end font-weight-bold">{{ number_format($service->cost, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $service->is_active ? 'success' : 'danger' }}">
                                        {{ $service->is_active ? __('messages.active') : __('messages.inactive') }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('healthcare.medical-services.edit', $service->id) }}" class="btn btn-datatable btn-icon btn-transparent-dark mr-2"><i class="fas fa-edit"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
