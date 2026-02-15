@extends('layouts.app')

@section('title', __('supplier_registration.title'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('supplier_registration.title') }}</h1>
            @can('supplier_registration.create')
                <a href="{{ route('purchases.supplier-registrations.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('supplier_registration.create') }}
                </a>
            @endcan
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('supplier_registration.registration_code') }}</th>
                                <th>{{ __('supplier_registration.company_name') }}</th>
                                <th>{{ __('supplier_registration.contact_person') }}</th>
                                <th>{{ __('supplier_registration.email') }}</th>
                                <th>{{ __('supplier_registration.phone') }}</th>
                                <th>{{ __('supplier_registration.status') }}</th>
                                <th>{{ __('general.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($registrations as $registration)
                                <tr>
                                    <td>
                                        <a href="{{ route('purchases.supplier-registrations.show', $registration) }}">
                                            {{ $registration->registration_code }}
                                        </a>
                                    </td>
                                    <td>{{ $registration->company_name }}</td>
                                    <td>{{ $registration->contact_person }}</td>
                                    <td>{{ $registration->email }}</td>
                                    <td>{{ $registration->phone }}</td>
                                    <td>
                                        @php
                                            $statusClass = [
                                                'pending' => 'warning',
                                                'under_review' => 'info',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                            ][$registration->status];
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ __('supplier_registration.status_' . $registration->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('purchases.supplier-registrations.show', $registration) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($registration->status === 'pending')
                                                @can('supplier_registration.edit')
                                                    <a href="{{ route('purchases.supplier-registrations.edit', $registration) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                            @endif
                                            @if($registration->status === 'approved' && !$registration->converted_vendor_id)
                                                @can('supplier_registration.approve')
                                                    <form
                                                        action="{{ route('purchases.supplier-registrations.convert', $registration) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success"
                                                            onclick="return confirm('{{ __('supplier_registration.confirm_convert') }}')">
                                                            <i class="fas fa-exchange-alt"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('supplier_registration.no_records') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $registrations->links() }}
            </div>
        </div>
    </div>
@endsection