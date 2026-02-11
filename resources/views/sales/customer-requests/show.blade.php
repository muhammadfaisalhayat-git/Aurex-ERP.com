@extends('layouts.app')

@section('title', __('messages.customer_request_details') ?? 'Customer Request Details')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('messages.customer_request_details') ?? 'Customer Request Details' }}</h1>
            <div class="btn-group">
                <a href="{{ route('sales.customer-requests.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back') }}
                </a>
                @if($customerRequest->status == 'pending')
                    <form action="{{ route('sales.customer-requests.convert', $customerRequest) }}" method="POST"
                        style="display:inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-exchange-alt me-1"></i> Convert to Quotation
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">General Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Document #</th>
                                <td>{{ $customerRequest->document_number }}</td>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <td>{{ $customerRequest->request_date->format('Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <th>Needed By</th>
                                <td>{{ $customerRequest->needed_date ? $customerRequest->needed_date->format('Y-m-d') : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span
                                        class="badge bg-{{ $customerRequest->status == 'pending' ? 'warning' : ($customerRequest->status == 'converted' ? 'success' : 'secondary') }}">
                                        {{ ucfirst($customerRequest->status) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Name</th>
                                <td>{{ $customerRequest->customer->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Branch</th>
                                <td>{{ $customerRequest->branch->name ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">System Info</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Created By</th>
                                <td>{{ $customerRequest->creator->name ?? 'System' }}</td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ $customerRequest->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Items</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customerRequest->items as $item)
                                <tr>
                                    <td>{{ $item->product->name_en ?? 'Unknown Product' }}</td>
                                    <td>{{ number_format($item->quantity, 2) }}</td>
                                    <td>{{ $item->notes ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No items found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection