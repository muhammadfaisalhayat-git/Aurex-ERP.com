@extends('layouts.app')

@section('title', 'Order Details - ' . $order->document_number)

@section('content')
<div class="page-header d-block d-md-flex">
    <div class="mb-3 mb-md-0">
        <h1 class="page-title">Order: {{ $order->document_number }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('production.orders.index') }}">Production Orders</a></li>
                <li class="breadcrumb-item active" aria-current="page">Details</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto d-flex gap-2">
        <a href="{{ route('production.orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
        @if($order->status !== 'completed' && $order->status !== 'cancelled')
            <form action="{{ route('production.orders.post', $order) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check-double me-2"></i>Complete & Post to GL
                </button>
            </form>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-4 col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title">General Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Product</label>
                    <div class="h5 mb-0 text-primary">{{ $order->product->name }}</div>
                    <div class="text-muted small">{{ $order->product->code }}</div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <label class="text-muted small text-uppercase fw-bold d-block mb-1">Quantity</label>
                        <div class="h6 mb-0">{{ number_format($order->quantity, 3) }}</div>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small text-uppercase fw-bold d-block mb-1">Status</label>
                        <span class="badge {{ $order->status === 'completed' ? 'bg-success' : 'bg-primary' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Cost Summary</label>
                    <div class="p-3 bg-light rounded border">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Unit Cost:</span>
                            <span class="fw-bold">{{ number_format($order->unit_cost, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-primary fw-bold pt-2 border-top">
                            <span>Total Value:</span>
                            <span>{{ number_format($order->total_cost, 2) }} USD</span>
                        </div>
                    </div>
                </div>

                @if($order->notes)
                    <div>
                        <label class="text-muted small text-uppercase fw-bold d-block mb-1">Internal Notes</label>
                        <p class="mb-0 small text-dark">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-lg-7">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title">Workflow & Operations</h5>
                <button class="btn btn-sm btn-primary">Add Work Order</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">Sequence</th>
                                <th>Work Center</th>
                                <th>Machine</th>
                                <th>Est. Hours</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($order->workOrders) > 0)
                                @foreach($order->workOrders as $index => $wo)
                                    <tr>
                                        <td class="ps-3"><span class="badge rounded-pill bg-secondary">{{ $index + 1 }}</span></td>
                                        <td>{{ $wo->workCenter->name ?? 'N/A' }}</td>
                                        <td>{{ $wo->machine->name ?? 'Any' }}</td>
                                        <td>{{ number_format($wo->estimated_hours, 1) }}h</td>
                                        <td><span class="text-muted small">{{ ucfirst($wo->status) }}</span></td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted small">No operations defined for this order.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Quality Assurance</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">Ref #</th>
                                <th>Inspector</th>
                                <th>Result</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($order->qualityControls) > 0)
                                @foreach($order->qualityControls as $qc)
                                    <tr>
                                        <td class="ps-3 text-mono">{{ $qc->id }}</td>
                                        <td>{{ $qc->inspector->name ?? 'System' }}</td>
                                        <td>
                                            <span class="badge {{ $qc->result === 'pass' ? 'bg-success' : 'bg-danger' }}">
                                                {{ strtoupper($qc->result) }}
                                            </span>
                                        </td>
                                        <td>{{ $qc->inspection_date->format('Y-m-d') }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">No QC records yet.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
