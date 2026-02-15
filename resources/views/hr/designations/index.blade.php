@extends('layouts.app')

@section('title', __('messages.designations'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.designations') }}</h1>
            <a href="{{ route('hr.designations.create') }}" class="btn btn-primary">
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
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($designations as $desig)
                                <tr>
                                    <td><code>{{ $desig->code }}</code></td>
                                    <td>{{ $desig->name }}</td>
                                    <td>{{ $desig->department->name ?? '-' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('hr.designations.show', $desig) }}"
                                                class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('hr.designations.edit', $desig) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        {{ __('messages.no_records_found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $designations->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection