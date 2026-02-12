@extends('layouts.app')

@section('title', __('messages.dashboard'))

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('messages.dashboard') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">{{ __('messages.home') }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#customizeDashboardModal">
                <i class="fas fa-cog me-2"></i>{{ __('messages.customize') }}
            </button>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row g-4 mb-4">
        @if(isset($widgetData['sales_today']))
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1">{{ __('messages.sales_today') }}</p>
                                <h3 class="mb-0">{{ number_format($widgetData['sales_today']['amount'], 2) }}</h3>
                                <small class="text-success">
                                    <i class="fas fa-arrow-up me-1"></i>{{ $widgetData['sales_today']['count'] }}
                                    {{ __('messages.invoices') }}
                                </small>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-dollar-sign text-primary fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($widgetData['sales_month']))
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1">{{ __('messages.sales_this_month') }}</p>
                                <h3 class="mb-0">{{ number_format($widgetData['sales_month']['amount'], 2) }}</h3>
                                <small class="text-muted">{{ $widgetData['sales_month']['count'] }}
                                    {{ __('messages.invoices') }}</small>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-chart-line text-success fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($widgetData['tax_collected']))
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1">{{ __('messages.tax_collected') }}</p>
                                <h3 class="mb-0">{{ number_format($widgetData['tax_collected']['month'], 2) }}</h3>
                                <small class="text-muted">{{ __('messages.today') }}:
                                    {{ number_format($widgetData['tax_collected']['today'], 2) }}</small>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="fas fa-calculator text-info fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($widgetData['pending_invoices']))
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1">{{ __('messages.pending_invoices') }}</p>
                                <h3 class="mb-0">{{ $widgetData['pending_invoices']['count'] }}</h3>
                                <small
                                    class="text-warning">{{ number_format($widgetData['pending_invoices']['amount'], 2) }}</small>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-clock text-warning fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Charts and Lists Row -->
    <div class="row g-4 mb-4">
        @if(isset($widgetData['top_customers']))
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('messages.top_customers') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @forelse($widgetData['top_customers'] as $customer)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <h6 class="mb-0">{{ $customer->customer?->name ?? 'N/A' }}</h6>
                                        <small class="text-muted">{{ $customer->customer?->code ?? 'N/A' }}</small>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">{{ number_format($customer->total, 2) }}</span>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p>{{ __('messages.no_data_available') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($widgetData['top_products']))
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('messages.top_products') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @forelse($widgetData['top_products'] as $product)
                                @php $productData = \App\Models\Product::find($product->product_id); @endphp
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <h6 class="mb-0">{{ $productData?->name ?? 'N/A' }}</h6>
                                        <small class="text-muted">{{ number_format($product->total_qty, 2) }}
                                            {{ __('messages.units_sold') }}</small>
                                    </div>
                                    <span
                                        class="badge bg-success rounded-pill">{{ number_format($product->total_amount, 2) }}</span>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p>{{ __('messages.no_data_available') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($widgetData['returns_summary']))
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('messages.returns_summary') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="p-3 bg-danger bg-opacity-10 rounded">
                                    <h4 class="text-danger mb-1">{{ $widgetData['returns_summary']['month_count'] }}</h4>
                                    <small class="text-muted">{{ __('messages.returns_count') }}</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-warning bg-opacity-10 rounded">
                                    <h4 class="text-warning mb-1">
                                        {{ number_format($widgetData['returns_summary']['month_amount'], 2) }}</h4>
                                    <small class="text-muted">{{ __('messages.returns_value') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 text-center">
                            <a href="{{ route('sales.returns.index') }}" class="btn btn-sm btn-outline-primary">
                                {{ __('messages.view_all_returns') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Pending Approvals -->
    @if(!empty($pendingApprovals))
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bell text-warning me-2"></i>{{ __('messages.pending_approvals') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if(isset($pendingApprovals['transfer_requests']) && $pendingApprovals['transfer_requests'] > 0)
                                <div class="col-md-3">
                                    <a href="{{ route('inventory.transfer-requests.index') }}"
                                        class="d-flex align-items-center p-3 bg-light rounded text-decoration-none">
                                        <div class="bg-warning bg-opacity-25 p-2 rounded me-3">
                                            <i class="fas fa-exchange-alt text-warning"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $pendingApprovals['transfer_requests'] }}</h6>
                                            <small class="text-muted">{{ __('messages.transfer_requests') }}</small>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Recent Activity (Super Admin Only) -->
    @if($isSuperAdmin && $recentActivities)
        <div class="row g-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('messages.recent_activity') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.action') }}</th>
                                        <th>{{ __('messages.entity') }}</th>
                                        <th>{{ __('messages.user') }}</th>
                                        <th>{{ __('messages.date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentActivities as $activity)
                                        <tr>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $activity->action === 'delete' ? 'danger' : ($activity->action === 'create' ? 'success' : 'primary') }}">
                                                    {{ __("actions.{$activity->action}") }}
                                                </span>
                                            </td>
                                            <td>{{ __("entities.{$activity->entity_type}") }} #{{ $activity->entity_id }}</td>
                                            <td>{{ $activity->user?->name ?? 'System' }}</td>
                                            <td>{{ $activity->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Customize Dashboard Modal -->
    <div class="modal fade" id="customizeDashboardModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('messages.customize_dashboard') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">{{ __('messages.select_widgets_to_display') }}</p>
                    <div class="list-group">
                        @foreach($widgets as $widget)
                            <label class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $widget->widget_name }}</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input widget-toggle" type="checkbox"
                                        data-widget="{{ $widget->widget_type }}" {{ $widget->is_visible ? 'checked' : '' }}>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                    <button type="button" class="btn btn-primary"
                        onclick="saveDashboardSettings()">{{ __('messages.save_changes') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function saveDashboardSettings() {
            const toggles = document.querySelectorAll('.widget-toggle');
            const settings = {};

            toggles.forEach(toggle => {
                settings[toggle.dataset.widget] = toggle.checked;
            });

            // Save settings via AJAX
            Object.entries(settings).forEach(([widget, isVisible]) => {
                fetch('{{ route("dashboard.widgets.toggle") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        widget_type: widget,
                        is_visible: isVisible
                    })
                });
            });

            // Close modal and reload
            bootstrap.Modal.getInstance(document.getElementById('customizeDashboardModal')).hide();
            location.reload();
        }
    </script>
@endpush