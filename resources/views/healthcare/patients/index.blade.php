@extends('layouts.app')

@section('title', __('messages.patient_list') . ' - ' . __('messages.healthcare_management'))

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.healthcare_management') }}</h1>
        <a href="{{ route('healthcare.patients.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus me-1"></i> {{ __('messages.add_patient') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.patient_list') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.code') }}</th>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.gender') }}</th>
                            <th>{{ __('messages.date_of_birth') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                            <tr>
                                <td><code>{{ $patient->code }}</code></td>
                                <td>{{ App::getLocale() == 'ar' ? ($patient->name_ar ?? $patient->name_en) : $patient->name_en }}</td>
                                <td>{{ __('messages.' . strtolower($patient->gender)) }}</td>
                                <td>{{ $patient->date_of_birth }}</td>
                                <td>
                                    <span class="badge bg-{{ $patient->is_active ? 'success' : 'danger' }}">
                                        {{ $patient->is_active ? __('messages.active') : __('messages.inactive') }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('healthcare.patients.edit', $patient->id) }}" class="btn btn-datatable btn-icon btn-transparent-dark mr-2"><i class="fas fa-edit"></i></a>
                                    <a href="{{ route('healthcare.patients.show', $patient->id) }}" class="btn btn-datatable btn-icon btn-transparent-dark"><i class="fas fa-eye"></i></a>
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
