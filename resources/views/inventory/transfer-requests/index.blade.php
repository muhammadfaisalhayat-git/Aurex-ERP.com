@extends('layouts.app')

@section('title', __('messages.transfer_requests'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.transfer_requests') }}</h1>
            <a href="{{ route('inventory.transfer-requests.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> {{ __('messages.create_transfer_request') }}
            </a>
        </div>

        <div class="card glassy">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('messages.document_number') }}</th>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.from_warehouse') }}</th>
                                <th>{{ __('messages.to_warehouse') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                                <tr>
                                    <td>{{ $request->document_number }}</td>
                                    <td>{{ $request->request_date->format('Y-m-d') }}</td>
                                    <td>{{ $request->fromWarehouse->name }}</td>
                                    <td>{{ $request->toWarehouse->name }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match ($request->status) {
                                                'executed' => 'paid',
                                                'approved' => 'posted',
                                                'pending' => 'draft',
                                                'rejected' => 'void',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge badge-{{ $badgeClass }}">
                                            {{ __('messages.' . $request->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('inventory.transfer-requests.show', $request) }}"
                                            class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                        <h4 class="text-muted">{{ __('messages.no_records_found') }}</h4>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $requests->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection