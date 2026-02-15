<aside class="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <i class="fas fa-cube"></i>
            <span>Aurex ERP</span>
        </a>
    </div>

    <nav class="sidebar-menu">
        <!-- Dashboard -->
        <div class="menu-item">
            <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>{{ __('messages.dashboard') }}</span>
            </a>
        </div>

        @canany(['view users', 'manage roles', 'manage settings'])
            <!-- Admin Section -->
            <div class="menu-section">{{ __('messages.administration') }}</div>

            @canany(['view users', 'manage roles'])
                <div
                    class="menu-item {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') ? 'open' : '' }}">
                    <a href="#" class="menu-link" data-submenu>
                        <i class="fas fa-users-cog"></i>
                        <span>{{ __('messages.user_management') }}</span>
                        <i class="fas fa-chevron-down menu-arrow"></i>
                    </a>
                    <div class="submenu">
                        @can('view users')
                            <a href="{{ route('admin.users.index') }}"
                                class="menu-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                {{ __('messages.users') }}
                            </a>
                        @endcan
                        @can('manage roles')
                            <a href="{{ route('admin.roles.index') }}"
                                class="menu-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                {{ __('messages.roles') }}
                            </a>
                        @endcan
                    </div>
                </div>
            @endcanany

            @canany(['view users']) {{-- Reusing view users for organization for now as per seeder modules logic --}}
                <div
                    class="menu-item {{ request()->routeIs('admin.branches.*') || request()->routeIs('admin.warehouses.*') ? 'open' : '' }}">
                    <a href="#" class="menu-link" data-submenu>
                        <i class="fas fa-building"></i>
                        <span>{{ __('messages.organization') }}</span>
                        <i class="fas fa-chevron-down menu-arrow"></i>
                    </a>
                    <div class="submenu">
                        @if(auth()->user()->hasRole('Super Admin'))
                            <a href="{{ route('admin.companies.index') }}"
                                class="menu-link {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}">
                                {{ __('messages.companies') }}
                            </a>
                        @endif
                        <a href="{{ route('admin.branches.index') }}"
                            class="menu-link {{ request()->routeIs('admin.branches.*') ? 'active' : '' }}">
                            {{ __('messages.branches') }}
                        </a>
                        <a href="{{ route('admin.warehouses.index') }}"
                            class="menu-link {{ request()->routeIs('admin.warehouses.*') ? 'active' : '' }}">
                            {{ __('messages.warehouses') }}
                        </a>
                    </div>
                </div>
            @endcanany

            @can('manage settings')
                <div class="menu-item">
                    <a href="{{ route('admin.settings.index') }}"
                        class="menu-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i>
                        <span>{{ __('messages.settings') }}</span>
                    </a>
                </div>
            @endcan
        @endcanany

        @canany(['view customers', 'view quotations', 'view invoices', 'view returns', 'view commissions', 'view customer_registration'])
            <!-- Sales Section -->
            <div class="menu-section">{{ __('messages.sales') }}</div>

            @can('view customers')
                <div class="menu-item">
                    <a href="{{ route('sales.customers.index') }}"
                        class="menu-link {{ request()->routeIs('sales.customers.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>{{ __('messages.customers') }}</span>
                    </a>
                </div>
            @endcan

            @canany(['view quotations', 'view customer_registration']) {{-- simplified check for section --}}
                <div
                    class="menu-item {{ request()->routeIs('sales.customer-requests.*') || request()->routeIs('sales.quotations.*') || request()->routeIs('sales.contracts.*') || request()->routeIs('sales.sales-orders.*') ? 'open' : '' }}">
                    <a href="#" class="menu-link" data-submenu>
                        <i class="fas fa-file-invoice"></i>
                        <span>{{ __('messages.sales_documents') }}</span>
                        <i class="fas fa-chevron-down menu-arrow"></i>
                    </a>
                    <div class="submenu">
                        <a href="{{ route('sales.customer-requests.index') }}"
                            class="menu-link {{ request()->routeIs('sales.customer-requests.*') ? 'active' : '' }}">
                            {{ __('messages.customer_requests') }}
                        </a>
                        @can('view quotations')
                            <a href="{{ route('sales.quotations.index') }}"
                                class="menu-link {{ request()->routeIs('sales.quotations.*') ? 'active' : '' }}">
                                {{ __('messages.quotations') }}
                            </a>
                        @endcan
                        <a href="{{ route('sales.sales-orders.index') }}"
                            class="menu-link {{ request()->routeIs('sales.sales-orders.*') ? 'active' : '' }}">
                            {{ __('messages.sales_orders') }}
                        </a>
                        <a href="{{ route('sales.contracts.index') }}"
                            class="menu-link {{ request()->routeIs('sales.contracts.*') ? 'active' : '' }}">
                            {{ __('messages.sales_contracts') }}
                        </a>
                    </div>
                </div>
            @endcanany

            @can('view invoices')
                <div class="menu-item">
                    <a href="{{ route('sales.invoices.index') }}"
                        class="menu-link {{ request()->routeIs('sales.invoices.*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>{{ __('messages.sales_invoices') }}</span>
                    </a>
                </div>
            @endcan

            @can('view returns')
                <div class="menu-item">
                    <a href="{{ route('sales.returns.index') }}"
                        class="menu-link {{ request()->routeIs('sales.returns.*') ? 'active' : '' }}">
                        <i class="fas fa-undo"></i>
                        <span>{{ __('messages.sales_returns') }}</span>
                    </a>
                </div>
            @endcan

            @can('view commissions')
                <div class="menu-item">
                    <a href="{{ route('sales.commissions.rules') }}"
                        class="menu-link {{ request()->routeIs('sales.commissions.*') ? 'active' : '' }}">
                        <i class="fas fa-percentage"></i>
                        <span>{{ __('messages.commissions') }}</span>
                    </a>
                </div>
            @endcan

            @can('view customer_registration')
                <div class="menu-item">
                    <a href="{{ route('sales.customer-registrations.index') }}"
                        class="menu-link {{ request()->routeIs('sales.customer-registrations.*') ? 'active' : '' }}">
                        <i class="fas fa-user-plus"></i>
                        <span>{{ __('messages.customer_registrations') }}</span>
                    </a>
                </div>
            @endcan
        @endcanany

        @canany(['view vendors', 'view purchases', 'view local_purchase', 'view supplier_registration'])
            <!-- Purchases Section -->
            <div class="menu-section">{{ __('messages.purchases') }}</div>

            @can('view vendors')
                <div class="menu-item">
                    <a href="{{ route('purchases.vendors.index') }}"
                        class="menu-link {{ request()->routeIs('purchases.vendors.*') ? 'active' : '' }}">
                        <i class="fas fa-truck"></i>
                        <span>{{ __('messages.vendors') }}</span>
                    </a>
                </div>
            @endcan

            @can('view purchases')
                <div class="menu-item">
                    <a href="{{ route('purchases.supply-orders.index') }}"
                        class="menu-link {{ request()->routeIs('purchases.supply-orders.*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list"></i>
                        <span>{{ __('messages.supply_orders') }}</span>
                    </a>
                </div>

                <div class="menu-item">
                    <a href="{{ route('purchases.invoices.index') }}"
                        class="menu-link {{ request()->routeIs('purchases.invoices.*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart"></i>
                        <span>{{ __('messages.purchase_invoices') }}</span>
                    </a>
                </div>
            @endcan

            @can('view local_purchase')
                <div class="menu-item">
                    <a href="{{ route('purchases.local-purchases.index') }}"
                        class="menu-link {{ request()->routeIs('purchases.local-purchases.*') ? 'active' : '' }}">
                        <i class="fas fa-store"></i>
                        <span>{{ __('messages.local_purchases') }}</span>
                    </a>
                </div>
            @endcan

            @can('view supplier_registration')
                <div class="menu-item">
                    <a href="{{ route('purchases.supplier-registrations.index') }}"
                        class="menu-link {{ request()->routeIs('purchases.supplier-registrations.*') ? 'active' : '' }}">
                        <i class="fas fa-user-plus"></i>
                        <span>{{ __('messages.supplier_registrations') }}</span>
                    </a>
                </div>
            @endcan
        @endcanany

        @canany(['view products', 'view inventory'])
            <!-- Inventory Section -->
            <div class="menu-section">{{ __('messages.inventory') }}</div>

            @can('view products')
                <div
                    class="menu-item {{ request()->routeIs('inventory.products.*') || request()->routeIs('inventory.categories.*') ? 'open' : '' }}">
                    <a href="#" class="menu-link" data-submenu>
                        <i class="fas fa-boxes"></i>
                        <span>{{ __('messages.products') }}</span>
                        <i class="fas fa-chevron-down menu-arrow"></i>
                    </a>
                    <div class="submenu">
                        <a href="{{ route('inventory.products.index') }}"
                            class="menu-link {{ request()->routeIs('inventory.products.*') ? 'active' : '' }}">
                            {{ __('messages.products') }}
                        </a>
                        <a href="{{ route('inventory.categories.index') }}"
                            class="menu-link {{ request()->routeIs('inventory.categories.*') ? 'active' : '' }}">
                            {{ __('messages.categories') }}
                        </a>
                        <a href="{{ route('inventory.barcodes.index') }}"
                            class="menu-link {{ request()->routeIs('inventory.barcodes.*') ? 'active' : '' }}">
                            {{ __('messages.barcode_generator') }}
                        </a>
                    </div>
                </div>
            @endcan

            @can('view inventory')
                <div
                    class="menu-item {{ request()->routeIs('inventory.stock-supply.*') || request()->routeIs('inventory.stock-receiving.*') || request()->routeIs('inventory.stock-transfers.*') ? 'open' : '' }}">
                    <a href="#" class="menu-link" data-submenu>
                        <i class="fas fa-warehouse"></i>
                        <span>{{ __('messages.stock_management') }}</span>
                        <i class="fas fa-chevron-down menu-arrow"></i>
                    </a>
                    <div class="submenu">
                        <a href="{{ route('inventory.stock-supply.index') }}"
                            class="menu-link {{ request()->routeIs('inventory.stock-supply.*') ? 'active' : '' }}">
                            {{ __('messages.stock_supply') }}
                        </a>
                        <a href="{{ route('inventory.stock-receiving.index') }}"
                            class="menu-link {{ request()->routeIs('inventory.stock-receiving.*') ? 'active' : '' }}">
                            {{ __('messages.stock_receiving') }}
                        </a>
                        <a href="{{ route('inventory.stock-transfers.index') }}"
                            class="menu-link {{ request()->routeIs('inventory.stock-transfers.*') ? 'active' : '' }}">
                            {{ __('messages.stock_transfers') }}
                        </a>
                        <a href="{{ route('inventory.transfer-requests.index') }}"
                            class="menu-link {{ request()->routeIs('inventory.transfer-requests.*') ? 'active' : '' }}">
                            {{ __('messages.transfer_requests') }}
                        </a>
                        <a href="{{ route('inventory.issue-orders.index') }}"
                            class="menu-link {{ request()->routeIs('inventory.issue-orders.*') ? 'active' : '' }}">
                            {{ __('messages.issue_orders') }}
                        </a>
                        <a href="{{ route('inventory.assemblies.index') }}"
                            class="menu-link {{ request()->routeIs('inventory.assemblies.*') ? 'active' : '' }}">
                            {{ __('messages.composite_assemblies') }}
                        </a>
                    </div>
                </div>

                <div class="menu-item">
                    <a href="{{ route('inventory.stock-ledger.index') }}"
                        class="menu-link {{ request()->routeIs('inventory.stock-ledger.*') ? 'active' : '' }}">
                        <i class="fas fa-list-alt"></i>
                        <span>{{ __('messages.stock_ledger') }}</span>
                    </a>
                </div>
            @endcan
        @endcanany

        @canany(['view employees', 'view departments', 'view designations'])
            <!-- Human Resources Section -->
            <div class="menu-section">{{ __('messages.human_resources') }}</div>

            <div class="menu-item {{ request()->routeIs('hr.*') ? 'open' : '' }}">
                <a href="#" class="menu-link" data-submenu>
                    <i class="fas fa-users"></i>
                    <span>{{ __('messages.human_resources') }}</span>
                    <i class="fas fa-chevron-down menu-arrow"></i>
                </a>
                <div class="submenu">
                    @can('view employees')
                        <a href="{{ route('hr.employees.index') }}"
                            class="menu-link {{ request()->routeIs('hr.employees.*') ? 'active' : '' }}">
                            {{ __('messages.employees') }}
                        </a>
                    @endcan
                    @can('view departments')
                        <a href="{{ route('hr.departments.index') }}"
                            class="menu-link {{ request()->routeIs('hr.departments.*') ? 'active' : '' }}">
                            {{ __('messages.departments') }}
                        </a>
                    @endcan
                    @can('view designations')
                        <a href="{{ route('hr.designations.index') }}"
                            class="menu-link {{ request()->routeIs('hr.designations.*') ? 'active' : '' }}">
                            {{ __('messages.designations') }}
                        </a>
                    @endcan
                </div>
            </div>
        @endcanany

        @can('view transport')
            <!-- Transport Section -->
            <div class="menu-section">{{ __('messages.transport') }}</div>

            <div class="menu-item">
                <a href="{{ route('transport.trailers.index') }}"
                    class="menu-link {{ request()->routeIs('transport.trailers.*') ? 'active' : '' }}">
                    <i class="fas fa-truck-moving"></i>
                    <span>{{ __('messages.trailers') }}</span>
                </a>
            </div>

            <div class="menu-item">
                <a href="{{ route('transport.orders.index') }}"
                    class="menu-link {{ request()->routeIs('transport.orders.*') ? 'active' : '' }}">
                    <i class="fas fa-shipping-fast"></i>
                    <span>{{ __('messages.transport_orders') }}</span>
                </a>
            </div>

            <div class="menu-item">
                <a href="{{ route('transport.contracts.index') }}"
                    class="menu-link {{ request()->routeIs('transport.contracts.*') ? 'active' : '' }}">
                    <i class="fas fa-file-contract"></i>
                    <span>{{ __('messages.transport_contracts') }}</span>
                </a>
            </div>

            <div class="menu-item">
                <a href="{{ route('transport.claims.index') }}"
                    class="menu-link {{ request()->routeIs('transport.claims.*') ? 'active' : '' }}">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>{{ __('messages.transport_claims') }}</span>
                </a>
            </div>
        @endcan

        @can('view maintenance')
            <!-- Maintenance Section -->
            <div class="menu-section">{{ __('messages.maintenance') }}</div>

            <div class="menu-item">
                <a href="{{ route('maintenance.workshops.index') }}"
                    class="menu-link {{ request()->routeIs('maintenance.workshops.*') ? 'active' : '' }}">
                    <i class="fas fa-tools"></i>
                    <span>{{ __('messages.workshops') }}</span>
                </a>
            </div>

            <div class="menu-item">
                <a href="{{ route('maintenance.vouchers.index') }}"
                    class="menu-link {{ request()->routeIs('maintenance.vouchers.*') ? 'active' : '' }}">
                    <i class="fas fa-wrench"></i>
                    <span>{{ __('messages.maintenance_vouchers') }}</span>
                </a>
            </div>
        @endcan

        @can('view reports')
            <!-- Reports Section -->
            <div class="menu-section">{{ __('messages.reports') }}</div>

            <div class="menu-item {{ request()->routeIs('reports.sales.*') ? 'open' : '' }}">
                <a href="#" class="menu-link" data-submenu>
                    <i class="fas fa-chart-line"></i>
                    <span>{{ __('messages.sales_reports') }}</span>
                    <i class="fas fa-chevron-down menu-arrow"></i>
                </a>
                <div class="submenu">
                    <a href="{{ route('reports.sales.index') }}"
                        class="menu-link {{ request()->routeIs('reports.sales.index') ? 'active' : '' }}">
                        {{ __('messages.overview') }}
                    </a>
                    <a href="{{ route('reports.sales.by-customer') }}"
                        class="menu-link {{ request()->routeIs('reports.sales.by-customer') ? 'active' : '' }}">
                        {{ __('messages.by_customer') }}
                    </a>
                    <a href="{{ route('reports.sales.by-item') }}"
                        class="menu-link {{ request()->routeIs('reports.sales.by-item') ? 'active' : '' }}">
                        {{ __('messages.by_item') }}
                    </a>
                    <a href="{{ route('reports.sales.date-wise') }}"
                        class="menu-link {{ request()->routeIs('reports.sales.date-wise') ? 'active' : '' }}">
                        {{ __('messages.date_wise') }}
                    </a>
                </div>
            </div>

            <div class="menu-item {{ request()->routeIs('reports.suppliers.*') ? 'open' : '' }}">
                <a href="#" class="menu-link" data-submenu>
                    <i class="fas fa-truck"></i>
                    <span>{{ __('messages.supplier_reports') }}</span>
                    <i class="fas fa-chevron-down menu-arrow"></i>
                </a>
                <div class="submenu">
                    <a href="{{ route('reports.suppliers.index') }}"
                        class="menu-link {{ request()->routeIs('reports.suppliers.index') ? 'active' : '' }}">
                        {{ __('messages.overview') }}
                    </a>
                    <a href="{{ route('reports.suppliers.by-code-name') }}"
                        class="menu-link {{ request()->routeIs('reports.suppliers.by-code-name') ? 'active' : '' }}">
                        {{ __('messages.by_code_name') }}
                    </a>
                    <a href="{{ route('reports.suppliers.local-purchases') }}"
                        class="menu-link {{ request()->routeIs('reports.suppliers.local-purchases') ? 'active' : '' }}">
                        {{ __('messages.local_purchases') }}
                    </a>
                    <a href="{{ route('reports.suppliers.purchase-summary') }}"
                        class="menu-link {{ request()->routeIs('reports.suppliers.purchase-summary') ? 'active' : '' }}">
                        {{ __('messages.purchase_summary') }}
                    </a>
                </div>
            </div>

            <div class="menu-item">
                <a href="{{ route('reports.tax.summary') }}"
                    class="menu-link {{ request()->routeIs('reports.tax.*') ? 'active' : '' }}">
                    <i class="fas fa-calculator"></i>
                    <span>{{ __('messages.tax_reports') }}</span>
                </a>
            </div>

            <div class="menu-item">
                <a href="{{ route('reports.inventory.valuation') }}"
                    class="menu-link {{ request()->routeIs('reports.inventory.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span>{{ __('messages.inventory_reports') }}</span>
                </a>
            </div>
        @endcan
    </nav>
</aside>