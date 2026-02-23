@extends('layouts.app')

@section('title', __('messages.supply_orders'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">{{ __('messages.supply_orders') }}</h1>
            @can('create supply orders')
                <a href="{{ route('purchases.supply-orders.create') }}" class="btn btn-primary d-flex align-items-center">
                    <i class="fas fa-plus me-2"></i> {{ __('messages.create') }}
                </a>
            @endcan
        </div>

        <!-- Filters -->
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body">
                <form action="{{ route('purchases.supply-orders.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">{{ __('messages.search') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" 
                                   placeholder="{{ __('messages.search_placeholder') }}" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">{{ __('messages.vendor') }}</label>
                        <select name="vendor_id" class="form-select select2">
                            <option value="">{{ __('messages.all_vendors') }}</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                    {{ app()->getLocale() == 'ar' ? $vendor->name_ar : $vendor->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">{{ __('messages.status') }}</label>
                        <select name="status" class="form-select">
                            <option value="">{{ __('messages.all_statuses') }}</option>
                            @foreach(['draft', 'sent', 'invoiced', 'cancelled'] as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ __('messages.' . $status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-secondary me-2 w-100">
                            <i class="fas fa-filter"></i> {{ __('messages.filter') }}
                        </button>
                        <a href="{{ route('purchases.supply-orders.index') }}" class="btn btn-light w-100 border">
                            {{ __('messages.reset') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Listing Card -->
        <div class="card shadow-sm border-0 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase fw-bold">
                            <tr>
                                <th class="px-4 py-3">{{ __('messages.document_number') }}</th>
                                <th class="py-3">{{ __('messages.date') }}</th>
                                <th class="py-3">{{ __('messages.vendor') }}</th>
                                <th class="py-3">{{ __('messages.warehouse') }}</th>
                                <th class="py-3">{{ __('messages.total') }}</th>
                                <th class="py-3 text-center">{{ __('messages.status') }}</th>
                                <th class="px-4 py-3 text-end">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($supplyOrders as $order)
                                <tr>
                                    <td class="px-4">
                                        <div class="fw-bold text-primary">{{ $order->document_number }}</div>
                                        <div class="small text-muted text-uppercase fw-bold" style="font-size: 10px;">{{ $order->order_number }}</div>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold">{{ $order->order_date->format('Y-m-d') }}</div>
                                        @if($order->expected_delivery_date)
                                            <div class="small text-muted" style="font-size: 11px;">
                                                <i class="far fa-clock me-1 text-warning"></i> {{ $order->expected_delivery_date->format('Y-m-d') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ app()->getLocale() == 'ar' ? ($order->vendor->name_ar ?? $order->vendor->name_en ?? '-') : ($order->vendor->name_en ?? '-') }}</div>
                                        <div class="small text-muted" style="font-size: 11px;">{{ $order->branch->name ?? '-' }}</div>
                                    </td>
                                    <td>{{ $order->warehouse->name ?? '-' }}</td>
                                    <td class="fw-bold">{{ number_format($order->total_amount, 2) }}</td>
                                    <td class="text-center">
                                        @php
                                            $statusColors = [
                                                'draft' => 'secondary text-dark border-secondary',
                                                'sent' => 'primary',
                                                'invoiced' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            $color = $statusColors[$order->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge border bg-{{ $order->status === 'draft' ? 'light' : $color }} {{ $order->status === 'draft' ? 'text-muted' : '' }} fw-semibold rounded-pill px-3">
                                            {{ __('messages.' . $order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-icon btn-light rounded-circle shadow-none border" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                <li><a class="dropdown-item" href="{{ route('purchases.supply-orders.show', $order) }}"><i class="fas fa-eye me-2 text-info"></i>{{ __('messages.view') }}</a></li>
                                                @can('edit supply orders')
                                                    <li><a class="dropdown-item" href="{{ route('purchases.supply-orders.edit', $order) }}"><i class="fas fa-edit me-2 text-primary"></i>{{ __('messages.edit') }}</a></li>
                                                @endcan
                                                <li><a class="dropdown-item" href="{{ route('purchases.supply-orders.print', $order) }}" target="_blank"><i class="fas fa-print me-2 text-secondary"></i>{{ __('messages.print') }}</a></li>
                                                @if($order->isDraft())
                                                    @can('delete supply orders')
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('purchases.supply-orders.destroy', $order) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash-alt me-2"></i>{{ __('messages.delete') }}</button>
                                                            </form>
                                                        </li>
                                                    @endcan
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="mb-3">
                                            <i class="fas fa-box-open fa-3x text-light"></i>
                                        </div>
                                        <h5 class="text-muted">{{ __('messages.no_records_found') }}</h5>
                                        @can('create supply orders')
                                            <a href="{{ route('purchases.supply-orders.create') }}" class="btn btn-primary mt-3 ">
                                                {{ __('messages.create_first_order') }}
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($supplyOrders->hasPages())
                <div class="card-footer bg-white border-top-0 py-3">
                    {{ $supplyOrders->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });
</script>
@endpush
