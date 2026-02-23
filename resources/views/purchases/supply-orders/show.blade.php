@extends('layouts.app')

@section('title', __('messages.supply_order_details'))

@section('content')
    <div class="container-fluid">
        <!-- Header Actions -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('purchases.supply-orders.index') }}" class="text-decoration-none">{{ __('messages.supply_orders') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $supplyOrder->document_number }}</li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0 fw-bold">{{ $supplyOrder->document_number }}</h1>
            </div>
            <div class="d-flex">
                <a href="{{ route('purchases.supply-orders.index') }}" class="btn btn-light border border-secondary text-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back_to_list') }}
                </a>
                
                @if($supplyOrder->isDraft())
                    @can('edit supply orders')
                        <a href="{{ route('purchases.supply-orders.edit', $supplyOrder) }}" class="btn btn-primary me-2">
                            <i class="fas fa-edit me-1"></i> {{ __('messages.edit') }}
                        </a>
                    @endcan
                    
                    @can('send supply orders')
                        <form action="{{ route('purchases.supply-orders.send', $supplyOrder) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success me-2 shadow-sm" onclick="return confirm('{{ __('messages.confirm_send_order') }}')">
                                <i class="fas fa-paper-plane me-1"></i> {{ __('messages.send_order') }}
                            </button>
                        </form>
                    @endcan
                @endif

                @if($supplyOrder->canBeInvoiced())
                    @can('convert supply orders')
                        <button type="button" class="btn btn-info text-white me-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#convertModal">
                            <i class="fas fa-file-invoice me-1"></i> {{ __('messages.convert_to_invoice') }}
                        </button>
                    @endcan
                @endif
                
                <a href="{{ route('purchases.supply-orders.print', $supplyOrder) }}" target="_blank" class="btn btn-secondary shadow-sm">
                    <i class="fas fa-print me-1"></i> {{ __('messages.print') }}
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Info Section -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <p class="small text-muted text-uppercase fw-bold mb-1">{{ __('messages.order_date') }}</p>
                                <p class="fw-bold mb-0">{{ $supplyOrder->order_date->format('Y-m-d') }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="small text-muted text-uppercase fw-bold mb-1">{{ __('messages.expected_delivery') }}</p>
                                <p class="fw-bold mb-0 text-warning">{{ $supplyOrder->expected_delivery_date ? $supplyOrder->expected_delivery_date->format('Y-m-d') : '-' }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="small text-muted text-uppercase fw-bold mb-1">{{ __('messages.status') }}</p>
                                @php
                                    $statusColors = [
                                        'draft' => 'secondary text-dark border-secondary',
                                        'sent' => 'primary',
                                        'invoiced' => 'success',
                                        'received' => 'info text-white',
                                        'partial' => 'warning text-dark',
                                        'cancelled' => 'danger'
                                    ];
                                    $color = $statusColors[$supplyOrder->status] ?? 'secondary';
                                @endphp
                                <span class="badge border bg-{{ $supplyOrder->status === 'draft' ? 'light' : $color }} {{ $supplyOrder->status === 'draft' ? 'text-muted' : '' }} fw-semibold rounded-pill px-3">
                                    {{ __('messages.' . $supplyOrder->status) }}
                                </span>
                            </div>
                            <div class="col-md-3">
                                <p class="small text-muted text-uppercase fw-bold mb-1">{{ __('messages.created_by') }}</p>
                                <p class="fw-bold mb-0">{{ $supplyOrder->creator->name ?? '-' }}</p>
                            </div>

                            <hr class="my-3 opacity-10">

                            <div class="col-md-6 border-end">
                                <h6 class="fw-bold mb-3 text-primary">{{ __('messages.vendor_information') }}</h6>
                                <p class="mb-1 fw-bold fs-5">{{ app()->getLocale() == 'ar' ? ($supplyOrder->vendor?->name_ar ?? $supplyOrder->vendor?->name_en ?? '-') : ($supplyOrder->vendor?->name_en ?? '-') }}</p>
                                <p class="text-muted mb-1"><i class="fas fa-map-marker-alt me-2"></i>{{ $supplyOrder->vendor?->address ?? '-' }}</p>
                                <p class="text-muted mb-0"><i class="fas fa-phone me-2"></i>{{ $supplyOrder->vendor?->phone ?? '-' }}</p>
                            </div>
                            <div class="col-md-6 ps-4">
                                <h6 class="fw-bold mb-3 text-primary">{{ __('messages.delivery_information') }}</h6>
                                <p class="mb-1 fs-6"><span class="fw-bold text-dark">{{ __('messages.branch') }}:</span> {{ $supplyOrder->branch->name ?? '-' }}</p>
                                <p class="mb-0 fs-6"><span class="fw-bold text-dark">{{ __('messages.warehouse') }}:</span> {{ $supplyOrder->warehouse->name ?? '-' }}</p>
                                <p class="mt-2 text-muted fw-semibold small"><i class="fas fa-hashtag me-2"></i>{{ __('messages.reference_number') }}: {{ $supplyOrder->order_number }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h5 class="card-title fw-bold mb-0">{{ __('messages.order_items') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted small text-uppercase fw-bold">
                                    <tr>
                                        <th class="px-4 py-3">#</th>
                                        <th class="py-3">{{ __('messages.product') }}</th>
                                        <th class="py-3 text-center">{{ __('messages.quantity') }}</th>
                                        <th class="py-3 text-center">{{ __('messages.unit_price') }}</th>
                                        <th class="py-3 text-center">{{ __('messages.discount') }} %</th>
                                        <th class="py-3 text-center">{{ __('messages.tax') }} %</th>
                                        <th class="px-4 py-3 text-end">{{ __('messages.total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($supplyOrder->items as $index => $item)
                                        <tr>
                                            <td class="px-4 text-muted small">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="fw-bold">{{ $item->product->name ?? '-' }}</div>
                                                <div class="small text-muted">{{ $item->product->code ?? '-' }}</div>
                                                @if($item->notes)
                                                    <div class="small text-info mt-1"><i class="fas fa-info-circle me-1"></i>{{ $item->notes }}</div>
                                                @endif
                                            </td>
                                            <td class="text-center fw-semibold">{{ number_format($item->quantity, 3) }}</td>
                                            <td class="text-center">{{ number_format($item->unit_price, 2) }}</td>
                                            <td class="text-center text-danger">{{ number_format($item->discount_percentage, 2) }}%</td>
                                            <td class="text-center">{{ number_format($item->tax_rate, 2) }}%</td>
                                            <td class="px-4 text-end fw-bold">{{ number_format($item->total_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if($supplyOrder->notes || $supplyOrder->terms_conditions)
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold mb-3"><i class="fas fa-sticky-note me-2 text-warning"></i>{{ __('messages.notes') }}</h6>
                                    <p class="mb-0 text-muted" style="white-space: pre-line;">{{ $supplyOrder->notes ?? __('messages.no_notes') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold mb-3"><i class="fas fa-gavel me-2 text-info"></i>{{ __('messages.terms_conditions') }}</h6>
                                    <p class="mb-0 text-muted" style="white-space: pre-line;">{{ $supplyOrder->terms_conditions ?? __('messages.no_terms') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar Info -->
            <div class="col-lg-3">
                <!-- Summary Card -->
                <div class="card shadow-sm border-0 bg-primary text-white mb-4 overflow-hidden" style="min-height: 200px;">
                    <div class="card-body p-4 position-relative z-index-1">
                        <i class="fas fa-file-invoice fa-2x position-absolute opacity-25" style="right: 15px; top: 15px;"></i>
                        <h6 class="text-uppercase fw-bold mb-4 opacity-75 small">{{ __('messages.order_summary') }}</h6>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="opacity-75 small">{{ __('messages.subtotal') }}</span>
                            <span class="fw-bold">{{ number_format($supplyOrder->subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="opacity-75 small">{{ __('messages.discount') }}</span>
                            <span class="fw-bold text-warning">-{{ number_format($supplyOrder->discount_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="opacity-75 small">{{ __('messages.tax') }}</span>
                            <span class="fw-bold">{{ number_format($supplyOrder->tax_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="opacity-75 small">{{ __('messages.shipping') }}</span>
                            <span class="fw-bold">{{ number_format($supplyOrder->shipping_amount, 2) }}</span>
                        </div>
                        
                        <hr class="border-white opacity-25 my-4">
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-uppercase">{{ __('messages.total') }}</h5>
                            <h3 class="fw-bold mb-0">{{ number_format($supplyOrder->total_amount, 2) }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Status History -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                        <h6 class="fw-bold mb-0"><i class="fas fa-history me-2 text-secondary"></i>{{ __('messages.status_history') }}</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="vertical-timeline ps-3 border-start">
                            @foreach($supplyOrder->statusHistory->sortByDesc('created_at') as $history)
                                <div class="timeline-item mb-4 position-relative">
                                    <div class="timeline-point rounded-circle bg-white border border-primary position-absolute" style="width: 12px; height: 12px; left: -21px; top: 4px;"></div>
                                    <p class="small fw-bold mb-0 text-primary">{{ __('messages.' . $history->status) }}</p>
                                    <p class="small text-muted mb-1">{{ $history->created_at->format('Y-m-d H:i') }}</p>
                                    <p class="small text-dark mb-0 fst-italic">{{ $history->notes }}</p>
                                    <p class="small text-muted mt-1 align-items-center d-flex fw-semibold border-top pt-1" style="font-size: 10px;">
                                        <i class="fas fa-user-circle me-1"></i> {{ $history->changer->name ?? 'System' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Convert Modal -->
    <div class="modal fade" id="convertModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('purchases.supply-orders.convert-to-invoice', $supplyOrder) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title fw-bold"><i class="fas fa-exchange-alt me-2"></i>{{ __('messages.convert_to_invoice') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted small mb-4">{{ __('messages.convert_invoice_instructions') }}</p>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">{{ __('messages.invoice_date') }}</label>
                            <input type="date" name="invoice_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold small text-muted">{{ __('messages.due_date') }}</label>
                            <input type="date" name="due_date" class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-info text-white px-4 fw-bold">{{ __('messages.confirm_convert') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .vertical-timeline { position: relative; }
    .timeline-item:last-child { margin-bottom: 0 !important; }
</style>
@endpush
