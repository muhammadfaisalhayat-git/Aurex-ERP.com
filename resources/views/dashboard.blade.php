@extends('layouts.app')

@section('title', __('messages.dashboard'))

@section('content')
    <style>
        .nav-section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #475569;
            margin: 30px 0 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .nav-section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .menu-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            height: 100%;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: #3b82f6;
        }

        .menu-card .icon-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }

        .menu-card:hover .icon-wrapper {
            transform: scale(1.1);
        }

        .menu-card .menu-label {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.95rem;
            margin: 0;
        }

        /* Color Variants */
        .card-admin .icon-wrapper {
            background: #eff6ff;
            color: #2563eb;
        }

        .card-sales .icon-wrapper {
            background: #f0fdf4;
            color: #16a34a;
        }

        .card-purchases .icon-wrapper {
            background: #fff7ed;
            color: #ea580c;
        }

        .card-inventory .icon-wrapper {
            background: #faf5ff;
            color: #9333ea;
        }

        .card-hr .icon-wrapper {
            background: #fff1f2;
            color: #e11d48;
        }

        .card-transport .icon-wrapper {
            background: #f0f9ff;
            color: #0284c7;
        }

        .card-maintenance .icon-wrapper {
            background: #f8fafc;
            color: #475569;
        }

        .card-reports .icon-wrapper {
            background: #f5f3ff;
            color: #7c3aed;
        }

        .header-center {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .greeting-text {
            color: #475569;
            font-size: 1.25rem;
            margin: 0;
            white-space: nowrap;
        }

        @media (max-width: 991px) {
            .header-center {
                display: none;
            }
        }
    </style>

    <div class="page-header d-flex align-items-center justify-content-between">
        <div>
            <h1 class="page-title">{{ __('messages.dashboard') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">{{ __('messages.home') }}</li>
                </ol>
            </nav>
        </div>

        @php
            $hour = now()->hour;
            if ($hour < 12) {
                $greetingKey = 'good_morning';
            } elseif ($hour < 17) {
                $greetingKey = 'good_afternoon';
            } else {
                $greetingKey = 'good_evening';
            }
        @endphp

        <div class="header-center">
            <h4 class="greeting-text">
                {{ __("messages.$greetingKey") }}, <strong>{{ auth()->user()->name }}</strong>
            </h4>
        </div>

        <div>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#customizeDashboardModal">
                <i class="fas fa-cog me-2"></i>{{ __('messages.customize') }}
            </button>
        </div>
    </div>

    @php
        $visibility = \App\Models\SystemSetting::where('group', 'module_visibility')->get()->pluck('value', 'key');
        $checkVisibility = function ($key) use ($visibility) {
            return $visibility->get($key, '1') == '1';
        };
        $isSuperAdmin = auth()->user()->hasRole('Super Admin');
    @endphp

    <!-- Navigation Menus Card-wise -->
    <div class="mb-5">
        @if($checkVisibility('module_administration'))
            @canany(['view users', 'manage roles', 'manage settings'])
                <div class="nav-section-title">
                    <i class="fas fa-user-shield text-primary"></i>
                    {{ __('messages.administration') }}
                </div>
                <div class="row g-4">
                    @if($checkVisibility('sidebar_user_management'))
                        @can('view users')
                            <div class="col-6 col-md-4 col-lg-4">
                                <a href="{{ route('acp.user-mgmt.users.index') }}" class="menu-card card-admin">
                                    <div class="icon-wrapper"><i class="fas fa-users-cog"></i></div>
                                    <p class="menu-label">{{ __('messages.users') }}</p>
                                </a>
                            </div>
                        @endcan
                        @can('manage roles')
                            <div class="col-6 col-md-4 col-lg-4">
                                <a href="{{ route('acp.user-mgmt.roles.index') }}" class="menu-card card-admin">
                                    <div class="icon-wrapper"><i class="fas fa-id-badge"></i></div>
                                    <p class="menu-label">{{ __('messages.roles') }}</p>
                                </a>
                            </div>
                        @endcan
                    @endif
                    @if($checkVisibility('sidebar_organization'))
                        @if(auth()->user()->hasRole('Super Admin'))
                            <div class="col-6 col-md-4 col-lg-4">
                                <a href="{{ route('acp.organization.companies.index') }}" class="menu-card card-admin">
                                    <div class="icon-wrapper"><i class="fas fa-building"></i></div>
                                    <p class="menu-label">{{ __('messages.companies') }}</p>
                                </a>
                            </div>
                        @endif
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('acp.organization.branches.index') }}" class="menu-card card-admin">
                                <div class="icon-wrapper"><i class="fas fa-code-branch"></i></div>
                                <p class="menu-label">{{ __('messages.branches') }}</p>
                            </a>
                        </div>
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('acp.organization.warehouses.index') }}" class="menu-card card-admin">
                                <div class="icon-wrapper"><i class="fas fa-warehouse"></i></div>
                                <p class="menu-label">{{ __('messages.warehouses') }}</p>
                            </a>
                        </div>
                    @endif
                    @if($checkVisibility('sidebar_settings'))
                        @can('manage settings')
                            <div class="col-6 col-md-4 col-lg-4">
                                <a href="{{ route('acp.system.settings.index') }}" class="menu-card card-admin">
                                    <div class="icon-wrapper"><i class="fas fa-cog"></i></div>
                                    <p class="menu-label">{{ __('messages.settings') }}</p>
                                </a>
                            </div>
                        @endcan
                        @if(auth()->user()->hasRole('Super Admin'))
                            <div class="col-6 col-md-4 col-lg-4">
                                <a href="{{ route('deployments.index') }}" class="menu-card card-admin">
                                    <div class="icon-wrapper"><i class="fas fa-server"></i></div>
                                    <p class="menu-label">{{ __('messages.deployments') ?? 'Deployments' }}</p>
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            @endcanany
        @endif

        @if($checkVisibility('module_sales'))
            @canany(['view customers', 'view quotations', 'view invoices', 'view returns', 'view commissions', 'view customer_registration'])
                <div class="nav-section-title">
                    <i class="fas fa-shopping-cart text-success"></i>
                    {{ __('messages.sales') }}
                </div>
                <div class="row g-4">
                    @if($checkVisibility('sidebar_customers'))
                        @can('view customers')
                            <div class="col-6 col-md-4 col-lg-4">
                                <a href="{{ route('sales.customers.index') }}" class="menu-card card-sales">
                                    <div class="icon-wrapper"><i class="fas fa-user-tie"></i></div>
                                    <p class="menu-label">{{ __('messages.customers') }}</p>
                                </a>
                            </div>
                        @endcan
                    @endif
                    @if($checkVisibility('sidebar_sales_documents'))
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('sales.quotations.index') }}" class="menu-card card-sales">
                                <div class="icon-wrapper"><i class="fas fa-file-invoice"></i></div>
                                <p class="menu-label">{{ __('messages.quotations') }}</p>
                            </a>
                        </div>
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('sales.sales-orders.index') }}" class="menu-card card-sales">
                                <div class="icon-wrapper"><i class="fas fa-shopping-cart"></i></div>
                                <p class="menu-label">{{ __('messages.sales_orders') }}</p>
                            </a>
                        </div>
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('sales.contracts.index') }}" class="menu-card card-sales">
                                <div class="icon-wrapper"><i class="fas fa-file-contract"></i></div>
                                <p class="menu-label">{{ __('messages.sales_contracts') }}</p>
                            </a>
                        </div>
                    @endif
                    @if($checkVisibility('sidebar_sales_invoices'))
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('sales.invoices.index') }}" class="menu-card card-sales">
                                <div class="icon-wrapper"><i class="fas fa-file-invoice-dollar"></i></div>
                                <p class="menu-label">{{ __('messages.sales_invoices') }}</p>
                            </a>
                        </div>
                    @endif
                    @if($checkVisibility('sidebar_sales_returns'))
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('sales.returns.index') }}" class="menu-card card-sales">
                                <div class="icon-wrapper"><i class="fas fa-undo"></i></div>
                                <p class="menu-label">{{ __('messages.sales_returns') }}</p>
                            </a>
                        </div>
                    @endif
                </div>
            @endcanany
        @endif

        @if($checkVisibility('module_purchases'))
            @canany(['view vendors', 'view purchases', 'view local_purchase', 'view supplier_registration', 'view purchase_invoices'])
                <div class="nav-section-title">
                    <i class="fas fa-shopping-basket text-warning"></i>
                    {{ __('messages.purchases') }}
                </div>
                <div class="row g-4">
                    @if($checkVisibility('sidebar_vendors'))
                        @can('view vendors')
                            <div class="col-6 col-md-4 col-lg-4">
                                <a href="{{ route('purchases.vendors.index') }}" class="menu-card card-purchases">
                                    <div class="icon-wrapper"><i class="fas fa-truck"></i></div>
                                    <p class="menu-label">{{ __('messages.vendors') }}</p>
                                </a>
                            </div>
                        @endcan
                    @endif
                    @if($checkVisibility('sidebar_supply_orders'))
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('purchases.supply-orders.index') }}" class="menu-card card-purchases">
                                <div class="icon-wrapper"><i class="fas fa-clipboard-list"></i></div>
                                <p class="menu-label">{{ __('messages.supply_orders') }}</p>
                            </a>
                        </div>
                    @endif
                    @if($checkVisibility('sidebar_purchase_invoices'))
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('purchases.invoices.index') }}" class="menu-card card-purchases">
                                <div class="icon-wrapper"><i class="fas fa-receipt"></i></div>
                                <p class="menu-label">{{ __('messages.purchase_invoices') }}</p>
                            </a>
                        </div>
                    @endif
                    @if($checkVisibility('sidebar_local_purchases'))
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('purchases.local-purchases.index') }}" class="menu-card card-purchases">
                                <div class="icon-wrapper"><i class="fas fa-store"></i></div>
                                <p class="menu-label">{{ __('messages.local_purchases') }}</p>
                            </a>
                        </div>
                    @endif
                </div>
            @endcanany
        @endif

        @if($checkVisibility('module_accounting'))
            @canany(['view journal_vouchers', 'view chart_of_accounts', 'view ledger'])
                <div class="nav-section-title">
                    <i class="fas fa-calculator text-primary"></i>
                    {{ __('messages.accounting_system') }}
                </div>
                <div class="row g-4">
                    @can('view journal_vouchers')
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('accounting.gl.transactions.jv.index') }}" class="menu-card card-admin">
                                <div class="icon-wrapper"><i class="fas fa-book"></i></div>
                                <p class="menu-label">{{ __('messages.journal_vouchers') }}</p>
                            </a>
                        </div>
                    @endcan
                    @can('view chart_of_accounts')
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('accounting.gl.coa.index') }}" class="menu-card card-admin">
                                <div class="icon-wrapper"><i class="fas fa-sitemap"></i></div>
                                <p class="menu-label">{{ __('messages.chart_of_accounts') }}</p>
                            </a>
                        </div>
                    @endcan
                </div>
            @endcanany
        @endif

        @if($checkVisibility('module_inventory'))
            @canany(['view products', 'view inventory'])
                <div class="nav-section-title">
                    <i class="fas fa-boxes text-purple" style="color: #9333ea;"></i>
                    {{ __('messages.inventory') }}
                </div>
                <div class="row g-4">
                    @if($checkVisibility('sidebar_products'))
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('inventory.products.index') }}" class="menu-card card-inventory">
                                <div class="icon-wrapper"><i class="fas fa-box"></i></div>
                                <p class="menu-label">{{ __('messages.products') }}</p>
                            </a>
                        </div>
                    @endif
                    @if($checkVisibility('sidebar_stock_management'))
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('inventory.stock-supply.index') }}" class="menu-card card-inventory">
                                <div class="icon-wrapper"><i class="fas fa-stream"></i></div>
                                <p class="menu-label">{{ __('messages.stock_management') }}</p>
                            </a>
                        </div>
                    @endif
                    @if($checkVisibility('sidebar_stock_ledger'))
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('inventory.stock-ledger.index') }}" class="menu-card card-inventory">
                                <div class="icon-wrapper"><i class="fas fa-list-alt"></i></div>
                                <p class="menu-label">{{ __('messages.stock_ledger') }}</p>
                            </a>
                        </div>
                    @endif
                </div>
            @endcanany
        @endif

        @if($checkVisibility('module_hr'))
            @canany(['view employees', 'view departments', 'view designations'])
                <div class="nav-section-title">
                    <i class="fas fa-users text-danger"></i>
                    {{ __('messages.human_resources') }}
                </div>
                <div class="row g-4">
                    @if($checkVisibility('sidebar_employees'))
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('hr.employees.index') }}" class="menu-card card-hr">
                                <div class="icon-wrapper"><i class="fas fa-user-friends"></i></div>
                                <p class="menu-label">{{ __('messages.employees') }}</p>
                            </a>
                        </div>
                    @endif
                    @if($checkVisibility('sidebar_departments'))
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('hr.departments.index') }}" class="menu-card card-hr">
                                <div class="icon-wrapper"><i class="fas fa-sitemap"></i></div>
                                <p class="menu-label">{{ __('messages.departments') }}</p>
                            </a>
                        </div>
                    @endif
                    @if($checkVisibility('sidebar_salaries'))
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('hr.salaries.index') }}" class="menu-card card-hr">
                                <div class="icon-wrapper"><i class="fas fa-money-check-alt"></i></div>
                                <p class="menu-label">{{ __('messages.salaries') }}</p>
                            </a>
                        </div>
                    @endif
                </div>
            @endcanany
        @endif

        @if($checkVisibility('module_transport'))
            @can('view transport')
                <div class="nav-section-title">
                    <i class="fas fa-truck-moving text-info"></i>
                    {{ __('messages.transport') }}
                </div>
                <div class="row g-4">
                    @if($checkVisibility('sidebar_trailers'))
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('transport.trailers.index') }}" class="menu-card card-transport">
                                <div class="icon-wrapper"><i class="fas fa-truck-moving"></i></div>
                                <p class="menu-label">{{ __('messages.trailers') }}</p>
                            </a>
                        </div>
                    @endif
                    @if($checkVisibility('sidebar_transport_orders'))
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('transport.orders.index') }}" class="menu-card card-transport">
                                <div class="icon-wrapper"><i class="fas fa-shipping-fast"></i></div>
                                <p class="menu-label">{{ __('messages.transport_orders') }}</p>
                            </a>
                        </div>
                    @endif
                    @if($checkVisibility('sidebar_transport_contracts'))
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('transport.contracts.index') }}" class="menu-card card-transport">
                                <div class="icon-wrapper"><i class="fas fa-file-contract"></i></div>
                                <p class="menu-label">{{ __('messages.transport_contracts') }}</p>
                            </a>
                        </div>
                    @endif
                </div>
            @endcan
        @endif

        @if($checkVisibility('module_maintenance'))
            @can('view maintenance')
                <div class="nav-section-title">
                    <i class="fas fa-tools text-secondary"></i>
                    {{ __('messages.maintenance') }}
                </div>
                <div class="row g-4">
                    @if($checkVisibility('sidebar_workshops'))
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('maintenance.workshops.index') }}" class="menu-card card-maintenance">
                                <div class="icon-wrapper"><i class="fas fa-tools"></i></div>
                                <p class="menu-label">{{ __('messages.workshops') }}</p>
                            </a>
                        </div>
                    @endif
                    @if($checkVisibility('sidebar_maintenance_vouchers'))
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('maintenance.vouchers.index') }}" class="menu-card card-maintenance">
                                <div class="icon-wrapper"><i class="fas fa-wrench"></i></div>
                                <p class="menu-label">{{ __('messages.maintenance_vouchers') }}</p>
                            </a>
                        </div>
                    @endif
                </div>
            @endcan
        @endif

        @if($checkVisibility('module_reports'))
            @can('view reports')
                <div class="nav-section-title">
                    <i class="fas fa-chart-bar text-purple" style="color: #7c3aed;"></i>
                    {{ __('messages.reports') }}
                </div>
                <div class="row g-4">
                    @if($checkVisibility('sidebar_sales_reports'))
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('reports.sales.index') }}" class="menu-card card-reports">
                                <div class="icon-wrapper"><i class="fas fa-chart-line"></i></div>
                                <p class="menu-label">{{ __('messages.sales_reports') }}</p>
                            </a>
                        </div>
                    @endif
                    @if($checkVisibility('sidebar_tax_reports'))
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('reports.tax.summary') }}" class="menu-card card-reports">
                                <div class="icon-wrapper"><i class="fas fa-calculator"></i></div>
                                <p class="menu-label">{{ __('messages.tax_reports') }}</p>
                            </a>
                        </div>
                    @endif
                    @if($checkVisibility('sidebar_inventory_reports'))
                        <div class="col-6 col-md-4 col-lg-4">
                            <a href="{{ route('reports.inventory.valuation') }}" class="menu-card card-reports">
                                <div class="icon-wrapper"><i class="fas fa-box"></i></div>
                                <p class="menu-label">{{ __('messages.inventory_reports') }}</p>
                            </a>
                        </div>
                    @endif
                </div>
            @endcan
        @endif
    </div>

    <!-- Stats Cards Row (Moved below navigation) -->
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

        @if(isset($widgetData['total_employees']))
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1">{{ __('messages.total_employees') }}</p>
                                <h3 class="mb-0">{{ $widgetData['total_employees']['count'] }}</h3>
                                <small class="text-success">{{ __('messages.active') }}</small>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-users text-primary fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($widgetData['new_hires_month']))
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1">{{ __('messages.new_hires_month') }}</p>
                                <h3 class="mb-0">{{ $widgetData['new_hires_month']['count'] }}</h3>
                                <small class="text-info">{{ __('messages.this_month') }}</small>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="fas fa-user-plus text-info fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($widgetData['active_departments']))
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1">{{ __('messages.active_departments') }}</p>
                                <h3 class="mb-0">{{ $widgetData['active_departments']['count'] }}</h3>
                                <small class="text-success">{{ __('messages.active') }}</small>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-sitemap text-success fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($widgetData['payroll_cost']))
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1">{{ __('messages.payroll_cost') }}</p>
                                <h3 class="mb-0">{{ number_format($widgetData['payroll_cost']['amount'], 2) }}</h3>
                                <small class="text-danger">{{ __('messages.monthly_est') }}</small>
                            </div>
                            <div class="bg-danger bg-opacity-10 p-3 rounded">
                                <i class="fas fa-money-bill-wave text-danger fa-lg"></i>
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
                                        {{ number_format($widgetData['returns_summary']['month_amount'], 2) }}
                                    </h4>
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
        @if(isset($widgetData['employee_distribution']))
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('messages.employee_distribution') }}</h5>
                        <span class="badge bg-primary rounded-pill">{{ $widgetData['total_employees']['count'] ?? '' }}
                            {{ __('messages.total') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="mt-2">
                            @php
                                $totalEmployees = $widgetData['total_employees']['count'] ?? 1;
                            @endphp
                            @forelse($widgetData['employee_distribution'] as $dept)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fw-medium text-dark">{{ $dept['label'] }}</span>
                                        <span class="text-muted small">{{ $dept['total'] }}
                                            ({{ round(($dept['total'] / $totalEmployees) * 100) }}%)</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar"
                                            style="width: {{ ($dept['total'] / $totalEmployees) * 100 }}%"
                                            aria-valuenow="{{ ($dept['total'] / $totalEmployees) * 100 }}" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-users-slash fa-2x mb-2"></i>
                                    <p>{{ __('messages.no_data_available') }}</p>
                                </div>
                            @endforelse
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
                <div class="modal-footer d-flex justify-content-between">
                    <form action="{{ route('dashboard.widgets.reset') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm"
                            onclick="return confirm('{{ __('messages.confirm_reset_dashboard') }}')">
                            <i class="fas fa-sync-alt me-1"></i> {{ __('messages.reset_to_defaults') }}
                        </button>
                    </form>
                    <div>
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                        <button type="button" class="btn btn-primary"
                            onclick="saveDashboardSettings()">{{ __('messages.save_changes') }}</button>
                    </div>
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