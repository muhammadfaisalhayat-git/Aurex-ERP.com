@extends('layouts.app')

@section('title', __('messages.sales_returns') ?? 'Sales Returns')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.sales_returns') ?? 'Sales Returns' }}</h1>
            <div>
                @can('create returns')
                    <a href="{{ route('sales.returns.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i> {{ __('messages.create') }}
                    </a>
                @endcan
            </div>
        </div>

        <div class="card mb-4 glassy">
            <div class="card-body">
                <form action="{{ route('sales.returns.index') }}" method="GET" class="row g-4">
                    <div class="col-md-4">
                        <label for="return_number" class="form-label fw-bold">{{ __('messages.return_number') ?? 'Return Number' }}</label>
                        <div class="input-group">
                            <input type="text" name="return_number" id="return_number" class="form-control bg-white"
                                value="{{ request('return_number') }}" placeholder="Search Return #">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="customer_id" class="form-label fw-bold">{{ __('messages.customer') }}</label>
                        <select name="customer_id" id="customer_id" class="form-select bg-white shadow-none">
                            <option value="">{{ __('messages.all_customers') }}</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label fw-bold">{{ __('messages.status') }}</label>
                        <select name="status" id="status" class="form-select bg-white shadow-none">
                            <option value="">{{ __('messages.all_statuses') }}</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('messages.draft') }}</option>
                            <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>{{ __('messages.posted') }}</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('messages.cancelled') }}</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="date_from" class="form-label fw-bold">{{ __('messages.date_from') }}</label>
                        <input type="date" name="date_from" id="date_from" class="form-control bg-white shadow-none"
                            value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="date_to" class="form-label fw-bold">{{ __('messages.date_to') }}</label>
                        <input type="date" name="date_to" id="date_to" class="form-control bg-white shadow-none"
                            value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i> {{ __('messages.search') }}
                        </button>
                        <a href="{{ route('sales.returns.index') }}" class="btn btn-outline-secondary">
                            {{ __('messages.reset') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('messages.return_number') ?? 'Return Number' }}</th>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.customer') }}</th>
                                <th>{{ __('messages.total') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($returns as $return)
                                <tr>
                                    <td>
                                        <a href="{{ route('sales.returns.show', $return) }}">
                                            {{ $return->return_number }}
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $return->document_number }}</small>
                                    </td>
                                    <td>{{ $return->return_date->format('Y-m-d') }}</td>
                                    <td>{{ $return->customer->name ?? '-' }}</td>
                                    <td>{{ number_format($return->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $return->status === 'posted' ? 'success' : ($return->status === 'draft' ? 'secondary' : 'danger') }}">
                                            {{ __('messages.' . $return->status) ?? $return->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @can('view returns')
                                                <a href="{{ route('sales.returns.show', $return) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">{{ __('messages.no_records_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $returns->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
