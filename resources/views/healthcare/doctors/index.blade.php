@extends('layouts.app')

@section('title', __('messages.doctor_list') . ' - ' . __('messages.healthcare_management'))

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.doctors') }}</h1>
        <a href="{{ route('healthcare.doctors.create') }}" class="btn btn-primary">
            <i class="fas fa-user-md me-1"></i> {{ __('messages.add_doctor') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.doctor_list') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.code') }}</th>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.specialization') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($doctors as $doctor)
                            <tr>
                                <td><code>{{ $doctor->code }}</code></td>
                                <td>{{ App::getLocale() == 'ar' ? ($doctor->name_ar ?? $doctor->name_en) : $doctor->name_en }}</td>
                                <td>{{ $doctor->specialization }}</td>
                                <td>
                                    <span class="badge bg-{{ $doctor->is_active ? 'success' : 'danger' }}">
                                        {{ $doctor->is_active ? __('messages.active') : __('messages.inactive') }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('healthcare.doctors.edit', $doctor->id) }}" class="btn btn-datatable btn-icon btn-transparent-dark mr-2"><i class="fas fa-edit"></i></a>
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
