@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a
                                href="{{ route('inventory.products.index') }}">{{ __('messages.products') }}</a></li>
                        <li class="breadcrumb-item active">{{ $product->code }}</li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0">{{ $product->name }}</h1>
                <div class="mt-1">
                    <span class="badge {{ ($product->available_stock ?? 0) > 0 ? 'bg-success' : 'bg-danger' }}">
                        {{ __('messages.stock') }}: {{ number_format($product->available_stock ?? 0, 2) }}
                    </span>
                </div>
            </div>
            <div>
                <a href="{{ route('inventory.products.edit', $product) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i> {{ __('messages.edit') }}
                </a>
                <a href="{{ route('inventory.products.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> {{ __('messages.back') }}
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white p-0">
                <ul class="nav nav-tabs nav-fill border-bottom-0" id="productTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="details-tab" data-bs-toggle="tab" href="#details" role="tab">
                            <i class="fas fa-info-circle me-1"></i> {{ __('messages.master_details') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="other-tab" data-bs-toggle="tab" href="#other" role="tab">
                            <i class="fas fa-plus-circle me-1"></i> {{ __('messages.other_data') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="flags-tab" data-bs-toggle="tab" href="#flags" role="tab">
                            <i class="fas fa-check-square me-1"></i> {{ __('messages.flags') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="movement-tab" data-bs-toggle="tab" href="#movement" role="tab">
                            <i class="fas fa-exchange-alt me-1"></i> {{ __('messages.movement') }}
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                <div class="tab-content" id="productTabsContent">
                    <!-- Details Tab -->
                    <div class="tab-pane fade show active" id="details" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3 border-bottom pb-2">{{ __('messages.basic_information') }}
                                        </h6>
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <th class="text-muted small" style="width: 150px;">
                                                    {{ __('messages.item_code') ?? __('messages.code') }}</th>
                                                <td class="fw-bold">{{ $product->code }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted small" style="width: 150px;">
                                                    {{ __('messages.name_en') }}</th>
                                                <td>{{ $product->name_en }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted small">{{ __('messages.name_ar') }}</th>
                                                <td>{{ $product->name_ar }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted small">{{ __('messages.category') }}</th>
                                                <td>{{ $product->category->name ?? '-' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3 border-bottom pb-2">{{ __('messages.section_identification') ?? 'Identification' }}</h6>
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <th class="text-muted small" style="width: 150px;">{{ __('messages.gtin') }}</th>
                                                <td>{{ $product->gtin ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted small">{{ __('messages.hsn_code') }}</th>
                                                <td>{{ $product->hsn_code ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted small">{{ __('messages.sku') }}</th>
                                                <td>{{ $product->sku ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted small">{{ __('messages.item_type') }}</th>
                                                <td><span
                                                        class="badge bg-soft-info text-info">{{ __('messages.' . $product->type) ?? ucfirst($product->type) }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3 border-bottom pb-2">{{ __('messages.section_pricing') }}</h6>
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <th class="text-muted small" style="width: 150px;">{{ __('messages.cost_price') }}</th>
                                                <td class="fw-bold">
                                                    {{ number_format((float) ($product->cost_price ?? 0), 2) }} {{ __('messages.sar') }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted small">{{ __('messages.sale_price') }}</th>
                                                <td class="fw-bold text-success">
                                                    {{ number_format((float) ($product->sale_price ?? 0), 2) }} {{ __('messages.sar') }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted small">{{ __('messages.tax_rate') }}</th>
                                                <td>{{ number_format($product->tax_rate ?? 0, 2) }}%</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-3 border-bottom pb-2">{{ __('messages.section_dimensions_weight') }}</h6>
                                        <div class="row g-2">
                                            <div class="col-6 small text-muted">{{ __('messages.length') }} (cm): <span
                                                    class="text-dark fw-bold">{{ $product->length ?? '0.00' }}</span></div>
                                            <div class="col-6 small text-muted">{{ __('messages.width') }} (cm): <span
                                                    class="text-dark fw-bold">{{ $product->width ?? '0.00' }}</span></div>
                                            <div class="col-6 small text-muted">{{ __('messages.height') }} (cm): <span
                                                    class="text-dark fw-bold">{{ $product->height ?? '0.00' }}</span></div>
                                            <div class="col-6 small text-muted">{{ __('messages.area') }} (m²): <span
                                                    class="text-dark fw-bold">{{ $product->area ?? '0.00' }}</span></div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <h6 class="fw-bold mb-3 border-bottom pb-2">{{ __('messages.description') }}</h6>
                                        <p class="small text-muted mb-0">
                                            {{ $product->description ?: __('messages.no_description') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 border-start">
                                <div class="p-3">
                                    <h6 class="fw-bold mb-3"><i class="fas fa-image me-1"></i>
                                        {{ __('messages.product_image') }}</h6>
                                    @if($product->image_path)
                                        <img src="{{ asset('storage/' . $product->image_path) }}"
                                            class="img-fluid rounded border shadow-sm" alt="{{ $product->name }} ({{ __('messages.stock') }}: {{ $product->available_stock }})">
                                    @else
                                        <div class="text-center py-5 bg-light rounded border border-dashed">
                                            <i class="fas fa-box-open fa-4x text-muted mb-2"></i>
                                            <div class="small text-muted">
                                                {{ __('messages.no_image_available') }}</div>
                                        </div>
                                    @endif

                                    <div class="mt-4 p-3 bg-light rounded shadow-sm border">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="small fw-bold">{{ __('messages.status') }}</span>
                                            @if($product->is_active)
                                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>
                                                    {{ __('messages.active') }}</span>
                                            @else
                                                <span class="badge bg-secondary"><i class="fas fa-times-circle me-1"></i>
                                                    {{ __('messages.inactive') }}</span>
                                            @endif
                                        </div>
                                        @if($product->deactivation_reason)
                                            <div class="mt-2 pt-2 border-top">
                                                <small class="text-danger fw-bold d-block">{{ __('messages.deactivation_reason') }}</small>
                                                <p class="small text-muted mb-0">{{ $product->deactivation_reason }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Other Data Tab -->
                    <div class="tab-pane fade" id="other" role="tabpanel">
                        <div class="row g-4">
                            {{-- Classification --}}
                            <div class="col-12 mb-2">
                                <h6 class="text-muted small fw-bold text-uppercase border-bottom pb-2"><i
                                        class="fas fa-tag me-1"></i> {{ __('messages.product_classification') }}</h6>
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted d-block">{{ __('messages.item_activity') }}</label>
                                <span class="fw-bold">{{ $product->item_activity ?: '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted d-block">{{ __('messages.brand') }}</label>
                                <span class="fw-bold">{{ $product->brand ?: '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted d-block">{{ __('messages.manufacturer_company') }}</label>
                                <span class="fw-bold">{{ $product->manufacturer_company ?: '-' }}</span>
                            </div>
                            {{-- Physical Attributes --}}
                            <div class="col-12 mt-3 mb-2">
                                <h6 class="text-muted small fw-bold text-uppercase border-bottom pb-2"><i
                                        class="fas fa-palette me-1"></i> {{ __('messages.physical_attributes') }}</h6>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted d-block">{{ __('messages.color') }}</label>
                                <span class="fw-bold">{{ $product->color ?: '-' }}</span>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted d-block">{{ __('messages.material') }}</label>
                                <span class="fw-bold">{{ $product->material ?: '-' }}</span>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted d-block">{{ __('messages.season') }}</label>
                                <span class="fw-bold">{{ $product->season ?: '-' }}</span>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted d-block">{{ __('messages.country_of_origin') }}</label>
                                <span class="fw-bold">{{ $product->country_of_origin ?: '-' }}</span>
                            </div>
                            {{-- Logistics --}}
                            <div class="col-12 mt-3 mb-2">
                                <h6 class="text-muted small fw-bold text-uppercase border-bottom pb-2"><i
                                        class="fas fa-boxes me-1"></i> {{ __('messages.logistics_lifecycle') }}</h6>
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted d-block">{{ __('messages.item_storage') }}</label>
                                <span class="fw-bold">{{ $product->items_storage ?: '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted d-block">{{ __('messages.inactivation_date') }}</label>
                                <span
                                    class="fw-bold text-danger">{{ $product->inactivation_date ? $product->inactivation_date->format('Y-m-d') : '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Flags Tab -->
                    <div class="tab-pane fade" id="flags" role="tabpanel">
                        <div class="row g-3">
                            @php
                                $flagFields = [
                                    'is_not_for_sale',
                                    'is_controlled',
                                    'allow_fractions',
                                    'is_service',
                                    'sold_in_cash',
                                    'is_asset',
                                    'use_partition',
                                    'is_compound',
                                    'is_component',
                                    'is_non_returnable',
                                    'use_expiry_date',
                                    'is_requirement',
                                    'show_in_vss',
                                    'use_custodians',
                                    'use_in_crm',
                                    'has_alternatives',
                                    'item_code_as_serial',
                                    'show_in_css',
                                    'is_weighted',
                                    'is_reserved'
                                ];
                            @endphp
                            @foreach($flagFields as $field)
                                <div class="col-md-3">
                                    <div
                                        class="p-3 border rounded @if($product->$field) bg-soft-success border-success @else bg-light @endif d-flex justify-content-between align-items-center">
                                        <span class="small fw-medium">{{ __('messages.' . $field) }}</span>
                                        @if($product->$field)
                                            <i class="fas fa-check-circle text-success"></i>
                                        @else
                                            <i class="fas fa-times-circle text-muted opacity-50"></i>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Movement Tab -->
                    <div class="tab-pane fade" id="movement" role="tabpanel">
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">{{ __('messages.from_date') }}</label>
                                        <input type="date" id="m_from_date" class="form-control form-control-sm"
                                            value="{{ $fromDate }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">{{ __('messages.to_date') }}</label>
                                        <input type="date" id="m_to_date" class="form-control form-control-sm"
                                            value="{{ $toDate }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">{{ __('messages.warehouse') }}</label>
                                        <select id="m_warehouse_id" class="form-select form-select-sm">
                                            <option value="">{{ __('messages.all_warehouses') }}</option>
                                            @foreach($warehouses as $warehouse)
                                                <option value="{{ $warehouse->id }}" {{ $warehouseId == $warehouse->id ? 'selected' : '' }}>
                                                    {{ $warehouse->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" onclick="applyMovementFilters()"
                                            class="btn btn-secondary btn-sm w-100">
                                            <i class="fas fa-filter me-1"></i> {{ __('messages.filter') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle border">
                                <thead>
                                    <tr>
                                        <th class="small fw-bold">{{ __('messages.doc_no') }}</th>
                                        <th class="small fw-bold">{{ __('messages.date') }}</th>
                                        <th class="small fw-bold">{{ __('messages.doc_type') }}</th>
                                        <th class="small fw-bold">{{ __('messages.wh_no') }}</th>
                                        <th class="small fw-bold">{{ __('messages.unit') }}</th>
                                        <th class="small fw-bold">{{ __('messages.package') }}</th>
                                        <th class="small fw-bold text-center">{{ __('messages.incoming_qty') }}</th>
                                        <th class="small fw-bold text-center">{{ __('messages.outgoing_qty') }}</th>
                                        <th class="small fw-bold text-center">{{ __('messages.foc_qty') }}</th>
                                        <th class="small fw-bold text-end">{{ __('messages.cost_price') }}</th>
                                        <th class="small fw-bold text-end">{{ __('messages.selling_price') }}</th>
                                        <th class="small fw-bold text-end">{{ __('messages.discount') }}</th>
                                        <th class="small fw-bold text-center">{{ __('messages.currency') }}</th>
                                        <th class="small fw-bold text-end">{{ __('messages.balance') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($movements as $movement)
                                        @php
                                            $routeName = null;
                                            $refTypeLower = strtolower($movement->reference_type);

                                            if ($refTypeLower === 'salesinvoice') {
                                                $routeName = 'sales.invoices.show';
                                            } elseif ($refTypeLower === 'salesreturn') {
                                                $routeName = 'sales.returns.show';
                                            } elseif ($refTypeLower === 'stocksupply') {
                                                $routeName = 'inventory.stock-supply.show';
                                            } elseif ($refTypeLower === 'stockreceiving') {
                                                $routeName = 'inventory.stock-receiving.show';
                                            } elseif ($refTypeLower === 'stocktransfer') {
                                                $routeName = 'inventory.stock-transfers.show';
                                            } elseif ($refTypeLower === 'stockissueorder' || $refTypeLower === 'issueorder') {
                                                $routeName = 'inventory.issue-orders.show';
                                            }
                                        @endphp
                                        <tr>
                                            <td class="small fw-bold">
                                                @if($routeName)
                                                    <a href="{{ route($routeName, $movement->reference_id) }}"
                                                        class="text-primary text-decoration-none">
                                                        <i class="fas fa-external-link-alt small me-1"></i>
                                                        {{ $movement->reference_number }}
                                                    </a>
                                                @else
                                                    {{ $movement->reference_number }}
                                                @endif
                                            </td>
                                            <td class="small">{{ $movement->transaction_date->format('Y-m-d') }}</td>
                                            <td class="small">
                                                <span class="badge bg-soft-info text-info">
                                                    {{ __('messages.' . strtolower($movement->reference_type)) }}
                                                </span>
                                            </td>
                                            <td class="small text-center">{{ $movement->warehouse->id }}</td>
                                            <td class="small text-center">{{ $product->default_unit ?? '-' }}</td>
                                            <td class="small text-center">1</td>
                                            <td class="small text-center text-success fw-bold">
                                                {{ $movement->movement_type == 'in' ? number_format((float) $movement->quantity, 2) : '0.00' }}
                                            </td>
                                            <td class="small text-center text-danger fw-bold">
                                                {{ $movement->movement_type == 'out' ? number_format((float) $movement->quantity, 2) : '0.00' }}
                                            </td>
                                            <td class="small text-center">0.00</td>
                                            <td class="small text-end">{{ number_format((float) $movement->unit_cost, 2) }}</td>
                                            <td class="small text-end">{{ number_format((float) $product->sale_price, 2) }}</td>
                                            <td class="small text-end">0.00</td>
                                            <td class="small text-center">{{ __('messages.sar') }}</td>
                                            <td
                                                class="small text-end fw-bold {{ $movement->balance_quantity < 0 ? 'text-danger' : 'text-primary' }}">
                                                {{ number_format((float) $movement->balance_quantity, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="14" class="text-center py-4 text-muted">
                                                <i class="fas fa-info-circle me-1"></i> {{ __('messages.no_movements_found') }}
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

@section('scripts')
    <script>
        function applyMovementFilters() {
            const fromDate = document.getElementById('m_from_date').value;
            const toDate = document.getElementById('m_to_date').value;
            const warehouseId = document.getElementById('m_warehouse_id').value;

            let url = new URL(window.location.href);
            url.searchParams.set('from_date', fromDate);
            url.searchParams.set('to_date', toDate);
            if (warehouseId) {
                url.searchParams.set('warehouse_id', warehouseId);
            } else {
                url.searchParams.delete('warehouse_id');
            }
            url.hash = 'movement';
            window.location.href = url.toString();
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (window.location.hash === '#movement') {
                var triggerEl = document.querySelector('#movement-tab');
                if (triggerEl) {
                    var tab = new bootstrap.Tab(triggerEl);
                    tab.show();
                }
            }
        });
    </script>
@endsection