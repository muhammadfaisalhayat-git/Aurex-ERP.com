@extends('layouts.app')

@section('title', __('messages.bill_of_materials') . ' - ' . $product->name)

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a
                                href="{{ route('inventory.products.index') }}">{{ __('messages.products') }}</a></li>
                        <li class="breadcrumb-item active">{{ $product->name }}</li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0">{{ __('messages.bill_of_materials') }}</h1>
            </div>
            <a href="{{ route('inventory.products.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card glassy mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('messages.product_details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small d-block">{{ __('messages.name') }}</label>
                            <span class="fw-bold">{{ $product->name }}</span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block">{{ __('messages.code') }}</label>
                            <code>{{ $product->code }}</code>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block">{{ __('messages.type') }}</label>
                            <span class="badge bg-primary">{{ ucfirst($product->type) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card glassy">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('messages.components') }}</h5>
                        @if($product->isComposite())
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addComponentModal">
                                <i class="fas fa-plus me-1"></i> {{ __('messages.add_component') }}
                            </button>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.component') }}</th>
                                        <th>{{ __('messages.quantity') }}</th>
                                        <th>{{ __('messages.waste_percentage') }}</th>
                                        <th>{{ __('messages.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($product->bomComponents as $bom)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $bom->component->name }}</div>
                                                <small class="text-muted">{{ $bom->component->code }}</small>
                                            </td>
                                            <td>{{ $bom->quantity }} {{ $bom->component->unit_of_measure }}</td>
                                            <td>{{ $bom->waste_percentage }}%</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <div class="text-muted mb-2">{{ __('messages.no_components_found') }}</div>
                                                @if(!$product->isComposite())
                                                    <div class="small text-warning">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        {{ __('messages.only_composite_products_can_have_bom') }}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection