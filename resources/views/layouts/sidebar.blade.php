<aside class="sidebar" id="main-sidebar" data-turbo-permanent>
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <i class="fas fa-cube"></i>
            <span>{{ __('messages.app_name') }}</span>
        </a>
    </div>

    @php
        $visibility = \App\Models\SystemSetting::where('group', 'module_visibility')->get()->pluck('value', 'key');
        $checkVisibility = function ($key) use ($visibility) {
            return $visibility->get($key, '1') == '1';
        };
    @endphp

    <nav class="sidebar-menu">
        <!-- Dashboard -->
        <div class="menu-item">
            <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt fa-fw"></i>
                <span>{{ __('messages.dashboard') }}</span>
            </a>
        </div>

        @if($checkVisibility('module_administration'))
            @canany(['view users', 'manage roles', 'manage settings'])
                <!-- Administrator Control Panel (ACP) -->
                <div class="menu-section">Administrator Control Panel</div>

                {{-- Organization Submenu --}}
                <div class="menu-item {{ request()->routeIs('acp.organization.*') ? 'open' : '' }}">
                    <button type="button" class="menu-link" data-submenu data-turbo="false">
                        <i class="fas fa-sitemap fa-fw"></i>
                        <span>Organization</span>
                        <i class="fas fa-chevron-down menu-arrow"></i>
                    </button>
                    <div class="submenu">
                        @if(auth()->user()->hasRole('Super Admin'))
                            <a href="{{ route('acp.organization.companies.index') }}"
                                class="menu-link {{ request()->routeIs('acp.organization.companies.*') ? 'active' : '' }}">
                                <i class="fas fa-building fa-fw me-2"></i> {{ __('messages.companies') }}
                            </a>
                        @endif
                        <a href="{{ route('acp.organization.branches.index') }}"
                            class="menu-link {{ request()->routeIs('acp.organization.branches.*') ? 'active' : '' }}">
                            <i class="fas fa-code-branch fa-fw me-2"></i> {{ __('messages.branches') }}
                        </a>
                        <a href="{{ route('acp.organization.warehouses.index') }}"
                            class="menu-link {{ request()->routeIs('acp.organization.warehouses.*') ? 'active' : '' }}">
                            <i class="fas fa-warehouse fa-fw me-2"></i> {{ __('messages.warehouses') }}
                        </a>
                    </div>
                </div>

                {{-- User Management Submenu --}}
                <div class="menu-item {{ request()->routeIs('acp.user-mgmt.*') ? 'open' : '' }}">
                    <button type="button" class="menu-link" data-submenu data-turbo="false">
                        <i class="fas fa-users-cog fa-fw"></i>
                        <span>User Management</span>
                        <i class="fas fa-chevron-down menu-arrow"></i>
                    </button>
                    <div class="submenu">
                        <a href="{{ route('acp.user-mgmt.users.index') }}"
                            class="menu-link {{ request()->routeIs('acp.user-mgmt.users.*') ? 'active' : '' }}">
                            <i class="fas fa-user fa-fw me-2"></i> {{ __('messages.users') }}
                        </a>
                        <a href="{{ route('acp.user-mgmt.roles.index') }}"
                            class="menu-link {{ request()->routeIs('acp.user-mgmt.roles.*') ? 'active' : '' }}">
                            <i class="fas fa-user-tag fa-fw me-2"></i> {{ __('messages.roles') }}
                        </a>
                        <a href="{{ route('acp.user-mgmt.user-groups.index') }}"
                            class="menu-link {{ request()->routeIs('acp.user-mgmt.user-groups.*') ? 'active' : '' }}">
                            <i class="fas fa-users fa-fw me-2"></i> User Groups
                        </a>
                        <a href="{{ route('acp.user-mgmt.user-profiles.index') }}"
                            class="menu-link {{ request()->routeIs('acp.user-mgmt.user-profiles.*') ? 'active' : '' }}">
                            <i class="fas fa-id-card fa-fw me-2"></i> User Profiles
                        </a>
                        <a href="{{ route('acp.user-mgmt.signatures.index') }}"
                            class="menu-link {{ request()->routeIs('acp.user-mgmt.signatures.*') ? 'active' : '' }}">
                            <i class="fas fa-signature fa-fw me-2"></i> Signatures
                        </a>
                    </div>
                </div>

                {{-- System Tools Submenu --}}
                <div class="menu-item {{ request()->routeIs('acp.system.*') ? 'open' : '' }}">
                    <button type="button" class="menu-link" data-submenu data-turbo="false">
                        <i class="fas fa-cogs fa-fw"></i>
                        <span>System Tools</span>
                        <i class="fas fa-chevron-down menu-arrow"></i>
                    </button>
                    <div class="submenu">
                        <a href="{{ route('acp.system.settings.index') }}"
                            class="menu-link {{ request()->routeIs('acp.system.settings.*') ? 'active' : '' }}">
                            <i class="fas fa-cog fa-fw me-2"></i> {{ __('messages.settings') }}
                        </a>
                        <a href="{{ route('acp.system.audit-logs.index') }}"
                            class="menu-link {{ request()->routeIs('acp.system.audit-logs.*') ? 'active' : '' }}">
                            <i class="fas fa-history fa-fw me-2"></i> Audit Logs
                        </a>
                        @can('manage backups')
                            <a href="{{ route('acp.system.backup.index') }}"
                                class="menu-link {{ request()->routeIs('acp.system.backup.*') ? 'active' : '' }}">
                                <i class="fas fa-database fa-fw me-2"></i> Backup Management
                            </a>
                        @endcan
                        @can('restore backups')
                            <a href="{{ route('acp.system.restore-backup.index') }}"
                                class="menu-link {{ request()->routeIs('acp.system.restore-backup.*') ? 'active' : '' }}">
                                <i class="fas fa-file-import fa-fw me-2"></i> Restore Backup
                            </a>
                        @endcan
                        <a href="{{ route('acp.system.alert-system.index') }}"
                            class="menu-link {{ request()->routeIs('acp.system.alert-system.*') ? 'active' : '' }}">
                            <i class="fas fa-bell fa-fw me-2"></i> Alert System
                        </a>
                        <a href="{{ route('acp.system.privileges.index') }}"
                            class="menu-link {{ request()->routeIs('acp.system.privileges.*') ? 'active' : '' }}">
                            <i class="fas fa-shield-alt fa-fw me-2"></i> Privileges
                        </a>
                        <a href="{{ route('acp.system.document-archive.index') }}"
                            class="menu-link {{ request()->routeIs('acp.system.document-archive.*') ? 'active' : '' }}">
                            <i class="fas fa-archive fa-fw me-2"></i> Document Archive
                        </a>
                        <a href="{{ route('acp.system.transaction-audit.index') }}"
                            class="menu-link {{ request()->routeIs('acp.system.transaction-audit.*') ? 'active' : '' }}">
                            <i class="fas fa-receipt fa-fw me-2"></i> Transaction Audit
                        </a>
                        <a href="{{ route('acp.system.mandatory-fields.index') }}"
                            class="menu-link {{ request()->routeIs('acp.system.mandatory-fields.*') ? 'active' : '' }}">
                            <i class="fas fa-asterisk fa-fw me-2"></i> Mandatory Fields
                        </a>
                        @if(auth()->user()->hasRole('Super Admin'))
                            <a href="{{ route('acp.system.deployments.index') }}"
                                class="menu-link {{ request()->routeIs('acp.system.deployments.*') ? 'active' : '' }}">
                                <i class="fas fa-server fa-fw me-2"></i> {{ __('messages.deployments') ?? 'Deployments' }}
                            </a>
                        @endif
                    </div>
                </div>
            @endcanany
        @endif

        @if($checkVisibility('module_crm'))
            @canany(['view crm leads', 'view crm opportunities', 'view crm pipeline'])
                <!-- CRM Section -->
                <div class="menu-section">{{ __('crm.crm') }}</div>

                <div class="menu-item {{ request()->routeIs('crm.*') ? 'open' : '' }}">
                    <button type="button" class="menu-link" data-submenu data-turbo="false">
                        <i class="fas fa-handshake fa-fw"></i>
                        <span>{{ __('crm.crm') }}</span>
                        <i class="fas fa-chevron-down menu-arrow"></i>
                    </button>
                    <div class="submenu">
                        <a href="{{ route('crm.pipeline.index') }}"
                            class="menu-link {{ request()->routeIs('crm.pipeline.*') ? 'active' : '' }}">
                            <i class="fas fa-th-large fa-fw me-2"></i> {{ __('crm.pipeline') }}
                        </a>
                        <a href="{{ route('crm.leads.index') }}"
                            class="menu-link {{ request()->routeIs('crm.leads.*') ? 'active' : '' }}">
                            <i class="fas fa-user-tag fa-fw me-2"></i> {{ __('crm.leads') }}
                        </a>
                        <a href="{{ route('crm.opportunities.index') }}"
                            class="menu-link {{ request()->routeIs('crm.opportunities.*') ? 'active' : '' }}">
                            <i class="fas fa-lightbulb fa-fw me-2"></i> {{ __('crm.opportunities') }}
                        </a>
                    </div>
                </div>
            @endcanany
        @endif

        @if($checkVisibility('module_sales'))
            @canany(['view customers', 'view quotations', 'view invoices', 'view returns', 'view commissions', 'view customer_registration'])
                <!-- Sales Section -->
                <div class="menu-section">{{ __('messages.sales') }}</div>

                @if($checkVisibility('sidebar_sales_documents'))
                    @canany(['view customers', 'view quotations', 'view customer_registration', 'view customer_requests', 'view sales_orders', 'view sales_contracts', 'view invoices', 'view returns', 'view commissions'])
                        <div
                            class="menu-item {{ request()->routeIs('sales.customers.*') || request()->routeIs('sales.invoices.*') || request()->routeIs('sales.returns.*') || request()->routeIs('sales.commissions.*') || request()->routeIs('sales.customer-registrations.*') || request()->routeIs('sales.customer-requests.*') || request()->routeIs('sales.quotations.*') || request()->routeIs('sales.contracts.*') || request()->routeIs('sales.sales-orders.*') ? 'open' : '' }}">
                            <button type="button" class="menu-link" data-submenu data-turbo="false">
                                <i class="fas fa-file-invoice fa-fw"></i>
                                <span>{{ __('messages.sales_documents') }}</span>
                                <i class="fas fa-chevron-down menu-arrow"></i>
                            </button>
                            <div class="submenu">
                                @if($checkVisibility('sidebar_customers'))
                                    @can('view customers')
                                        <a href="{{ route('sales.customers.index') }}"
                                            class="menu-link {{ request()->routeIs('sales.customers.*') ? 'active' : '' }}">
                                            <i class="fas fa-users fa-fw me-2"></i> {{ __('messages.customers') }}
                                        </a>
                                    @endcan
                                @endif

                                <a href="{{ route('sales.customer-requests.index') }}"
                                    class="menu-link {{ request()->routeIs('sales.customer-requests.*') ? 'active' : '' }}">
                                    <i class="fas fa-file-medical fa-fw me-2"></i> {{ __('messages.customer_requests') }}
                                </a>
                                @can('view quotations')
                                    <a href="{{ route('sales.quotations.index') }}"
                                        class="menu-link {{ request()->routeIs('sales.quotations.*') ? 'active' : '' }}">
                                        <i class="fas fa-file-alt fa-fw me-2"></i> {{ __('messages.quotations') }}
                                    </a>
                                @endcan
                                <a href="{{ route('sales.sales-orders.index') }}"
                                    class="menu-link {{ request()->routeIs('sales.sales-orders.*') ? 'active' : '' }}">
                                    <i class="fas fa-shopping-cart fa-fw me-2"></i> {{ __('messages.sales_orders') }}
                                </a>
                                <a href="{{ route('sales.contracts.index') }}"
                                    class="menu-link {{ request()->routeIs('sales.contracts.*') ? 'active' : '' }}">
                                    <i class="fas fa-file-contract fa-fw me-2"></i> {{ __('messages.sales_contracts') }}
                                </a>

                                @if($checkVisibility('sidebar_sales_invoices'))
                                    @can('view invoices')
                                        <a href="{{ route('sales.invoices.index') }}"
                                            class="menu-link {{ request()->routeIs('sales.invoices.*') ? 'active' : '' }}">
                                            <i class="fas fa-file-invoice-dollar fa-fw me-2"></i>
                                            {{ __('messages.sales_invoices') }}
                                        </a>
                                    @endcan
                                @endif

                                @if($checkVisibility('sidebar_sales_returns'))
                                    @can('view returns')
                                        <a href="{{ route('sales.returns.index') }}"
                                            class="menu-link {{ request()->routeIs('sales.returns.*') ? 'active' : '' }}">
                                            <i class="fas fa-undo fa-fw me-2"></i> {{ __('messages.sales_returns') }}
                                        </a>
                                    @endcan
                                @endif

                                @if($checkVisibility('sidebar_commissions'))
                                    @can('view commissions')
                                        <a href="{{ route('sales.commissions.rules') }}"
                                            class="menu-link {{ request()->routeIs('sales.commissions.*') ? 'active' : '' }}">
                                            <i class="fas fa-percentage fa-fw me-2"></i> {{ __('messages.commissions') }}
                                        </a>
                                    @endcan
                                @endif
                            </div>
                        </div>
                    @endcanany
                @endif
            @endcanany
        @endif

        @if($checkVisibility('module_purchases'))
            @canany(['view vendors', 'view purchases', 'view local_purchase', 'view supplier_registration', 'view purchase_invoices'])
                <!-- Purchases Section -->
                <div class="menu-section">{{ __('messages.purchases') }}</div>

                @if($checkVisibility('sidebar_vendors'))
                    @can('view vendors')
                        <div class="menu-item">
                            <a href="{{ route('purchases.vendors.index') }}"
                                class="menu-link {{ request()->routeIs('purchases.vendors.*') ? 'active' : '' }}">
                                <i class="fas fa-truck fa-fw"></i>
                                <span>{{ __('messages.vendors') }}</span>
                            </a>
                    @endcan
                @endif

                    @if($checkVisibility('sidebar_supply_orders'))
                        @canany(['view purchases', 'view purchase_invoices'])
                            <div class="menu-item">
                                <a href="{{ route('purchases.supply-orders.index') }}"
                                    class="menu-link {{ request()->routeIs('purchases.supply-orders.*') ? 'active' : '' }}">
                                    <i class="fas fa-clipboard-list fa-fw"></i>
                                    <span>{{ __('messages.supply_orders') }}</span>
                                </a>

                                @if($checkVisibility('sidebar_purchase_invoices'))
                                    @can('view purchase_invoices')
                                        <div class="menu-item">
                                            <a href="{{ route('purchases.invoices.index') }}"
                                                class="menu-link {{ request()->routeIs('purchases.invoices.*') ? 'active' : '' }}">
                                                <i class="fas fa-shopping-cart fa-fw me-2"></i>
                                                {{ __('messages.purchase_invoices') }}
                                            </a>
                                        </div>
                                    @endcan
                                @endif
                        @endcanany
                    @endif

                        @if($checkVisibility('sidebar_local_purchases'))
                            @can('view local_purchase')
                                <div class="menu-item">
                                    <a href="{{ route('purchases.local-purchases.index') }}"
                                        class="menu-link {{ request()->routeIs('purchases.local-purchases.*') ? 'active' : '' }}">
                                        <i class="fas fa-store fa-fw"></i>
                                        <span>{{ __('messages.local_purchases') }}</span>
                                    </a>
                            @endcan
                        @endif


            @endcanany
        @endif

                    @if($checkVisibility('module_inventory'))
                        @canany(['view products', 'view inventory'])
                            <!-- Inventory Section -->
                            <div class="menu-section">{{ __('messages.inventory') }}</div>

                            @if($checkVisibility('sidebar_products'))
                                @can('view products')
                                    <div
                                        class="menu-item {{ request()->routeIs('inventory.products.*') || request()->routeIs('inventory.categories.*') || request()->routeIs('inventory.barcodes.*') ? 'open' : '' }}">
                                        <button type="button" class="menu-link" data-submenu data-turbo="false">
                                            <i class="fas fa-boxes fa-fw"></i>
                                            <span>{{ __('messages.products') }}</span>
                                            <i class="fas fa-chevron-down menu-arrow"></i>
                                        </button>
                                        <div class="submenu">
                                            <a href="{{ route('inventory.products.index') }}"
                                                class="menu-link {{ request()->routeIs('inventory.products.*') ? 'active' : '' }}">
                                                <i class="fas fa-box fa-fw me-2"></i> {{ __('messages.products') }}
                                            </a>
                                            <a href="{{ route('inventory.categories.index') }}"
                                                class="menu-link {{ request()->routeIs('inventory.categories.*') ? 'active' : '' }}">
                                                <i class="fas fa-tags fa-fw me-2"></i> {{ __('messages.categories') }}
                                            </a>
                                            <a href="{{ route('inventory.measurement.units.index') }}"
                                                class="menu-link {{ request()->routeIs('inventory.measurement.units.*') ? 'active' : '' }}">
                                                <i class="fas fa-balance-scale fa-fw me-2"></i> Measurement Units
                                            </a>
                                            <a href="{{ route('inventory.barcodes.index') }}"
                                                class="menu-link {{ request()->routeIs('inventory.barcodes.*') ? 'active' : '' }}">
                                                <i class="fas fa-barcode fa-fw me-2"></i>
                                                {{ __('messages.barcode_generator') }}
                                            </a>
                                        </div>
                                    </div>
                                @endcan
                            @endif

                            @if($checkVisibility('sidebar_stock_management'))
                                @can('view inventory')
                                    <div
                                        class="menu-item {{ request()->routeIs('inventory.stock-supply.*') || request()->routeIs('inventory.stock-receiving.*') || request()->routeIs('inventory.stock-transfers.*') || request()->routeIs('inventory.transfer-requests.*') || request()->routeIs('inventory.issue-orders.*') || request()->routeIs('inventory.composite-assemblies.*') ? 'open' : '' }}">
                                        <button type="button" class="menu-link" data-submenu data-turbo="false">
                                            <i class="fas fa-warehouse fa-fw"></i>
                                            <span>{{ __('messages.stock_management') }}</span>
                                            <i class="fas fa-chevron-down menu-arrow"></i>
                                        </button>
                                        <div class="submenu">
                                            <a href="{{ route('inventory.stock-supply.index') }}"
                                                class="menu-link {{ request()->routeIs('inventory.stock-supply.*') ? 'active' : '' }}">
                                                <i class="fas fa-truck-loading fa-fw me-2"></i>
                                                {{ __('messages.stock_supply') }}
                                            </a>
                                            <a href="{{ route('inventory.stock-receiving.index') }}"
                                                class="menu-link {{ request()->routeIs('inventory.stock-receiving.*') ? 'active' : '' }}">
                                                <i class="fas fa-box-open fa-fw me-2"></i>
                                                {{ __('messages.stock_receiving') }}
                                            </a>
                                            <a href="{{ route('inventory.stock-transfers.index') }}"
                                                class="menu-link {{ request()->routeIs('inventory.stock-transfers.*') ? 'active' : '' }}">
                                                <i class="fas fa-exchange-alt fa-fw me-2"></i>
                                                {{ __('messages.stock_transfers') }}
                                            </a>
                                            <a href="{{ route('inventory.transfer-requests.index') }}"
                                                class="menu-link {{ request()->routeIs('inventory.transfer-requests.*') ? 'active' : '' }}">
                                                <i class="fas fa-clipboard-check fa-fw me-2"></i>
                                                {{ __('messages.transfer_requests') }}
                                            </a>
                                            <a href="{{ route('inventory.issue-orders.index') }}"
                                                class="menu-link {{ request()->routeIs('inventory.issue-orders.*') ? 'active' : '' }}">
                                                <i class="fas fa-sign-out-alt fa-fw me-2"></i>
                                                {{ __('messages.issue_orders') }}
                                            </a>
                                            <a href="{{ route('inventory.composite-assemblies.index') }}"
                                                class="menu-link {{ request()->routeIs('inventory.composite-assemblies.*') ? 'active' : '' }}">
                                                <i class="fas fa-cubes fa-fw me-2"></i>
                                                {{ __('messages.composite_assemblies') }}
                                            </a>
                                        </div>
                                    </div>
                                @endcan
                            @endif

                            @if($checkVisibility('sidebar_stock_ledger'))
                                <div class="menu-item">
                                    <a href="{{ route('inventory.stock-ledger.index') }}"
                                        class="menu-link {{ request()->routeIs('inventory.stock-ledger.*') ? 'active' : '' }}">
                                        <i class="fas fa-list-alt fa-fw"></i>
                                        <span>{{ __('messages.stock_ledger') }}</span>
                                    </a>
                                </div>
                            @endif
                        @endcanany
                    @endif

                    <!-- Production Section -->
                    <div class="menu-section">{{ __('messages.production') }}</div>
                    <div class="menu-item {{ request()->routeIs('production.*') ? 'open' : '' }}">
                        <button type="button" class="menu-link" data-submenu data-turbo="false">
                            <i class="fas fa-industry fa-fw"></i>
                            <span>{{ __('messages.production') }}</span>
                            <i class="fas fa-chevron-down menu-arrow"></i>
                        </button>
                        <div class="submenu">
                            <a href="{{ route('production.work-centers.index') }}"
                                class="menu-link {{ request()->routeIs('production.work-centers.*') ? 'active' : '' }}">
                                <i class="fas fa-map-marker-alt fa-fw me-2"></i>
                                {{ __('messages.work_centers') }}
                            </a>
                            <a href="{{ route('production.machines.index') }}"
                                class="menu-link {{ request()->routeIs('production.machines.*') ? 'active' : '' }}">
                                <i class="fas fa-cog fa-fw me-2"></i> {{ __('messages.machines') }}
                            </a>
                            <a href="{{ route('production.orders.index') }}"
                                class="menu-link {{ request()->routeIs('production.orders.*') ? 'active' : '' }}">
                                <i class="fas fa-list-ol fa-fw me-2"></i>
                                {{ __('messages.production_orders') }}
                            </a>
                        </div>
                    </div>

                    @if($checkVisibility('module_hr'))
                        @canany(['view employees', 'view departments', 'view designations'])
                            <!-- Human Resources Section -->
                            <div class="menu-section">{{ __('messages.human_resources') }}</div>

                            <div
                                class="menu-item {{ request()->routeIs('hr.*') || request()->routeIs('hr.employees.*') || request()->routeIs('hr.departments.*') || request()->routeIs('hr.designations.*') || request()->routeIs('hr.salaries.*') || request()->routeIs('hr.experience.*') ? 'open' : '' }}">
                                <button type="button" class="menu-link" data-submenu data-turbo="false">
                                    <i class="fas fa-users fa-fw"></i>
                                    <span>{{ __('messages.human_resources') }}</span>
                                    <i class="fas fa-chevron-down menu-arrow"></i>
                                </button>
                                <div class="submenu">
                                    @if($checkVisibility('sidebar_employees'))
                                        @can('view employees')
                                            <a href="{{ route('hr.employees.index') }}"
                                                class="menu-link {{ request()->routeIs('hr.employees.*') ? 'active' : '' }}">
                                                <i class="fas fa-id-badge fa-fw me-2"></i> {{ __('messages.employees') }}
                                            </a>
                                        @endcan
                                    @endif
                                    @if($checkVisibility('sidebar_departments'))
                                        @can('view departments')
                                            <a href="{{ route('hr.departments.index') }}"
                                                class="menu-link {{ request()->routeIs('hr.departments.*') ? 'active' : '' }}">
                                                <i class="fas fa-sitemap fa-fw me-2"></i> {{ __('messages.departments') }}
                                            </a>
                                        @endcan
                                    @endif
                                    @if($checkVisibility('sidebar_designations'))
                                        @can('view designations')
                                            <a href="{{ route('hr.designations.index') }}"
                                                class="menu-link {{ request()->routeIs('hr.designations.*') ? 'active' : '' }}">
                                                <i class="fas fa-briefcase fa-fw me-2"></i>
                                                {{ __('messages.designations') }}
                                            </a>
                                        @endcan
                                    @endif
                                    @if($checkVisibility('sidebar_salaries'))
                                        @can('view employees')
                                            <a href="{{ route('hr.salaries.index') }}"
                                                class="menu-link {{ request()->routeIs('hr.salaries.*') ? 'active' : '' }}">
                                                <i class="fas fa-money-bill-wave fa-fw me-2"></i>
                                                {{ __('messages.salaries') }}
                                            </a>
                                        @endcan
                                    @endif
                                    @if($checkVisibility('sidebar_experience_letters'))
                                        @can('view employees')
                                            <a href="{{ route('hr.experience.index') }}"
                                                class="menu-link {{ request()->routeIs('hr.experience.*') ? 'active' : '' }}">
                                                <i class="fas fa-envelope-open-text fa-fw me-2"></i>
                                                {{ __('messages.experience_letters') }}
                                            </a>
                                        @endcan
                                    @endif

                                    @can('view leave requests')
                                        <a href="{{ route('hr.leaves.index') }}"
                                            class="menu-link {{ request()->routeIs('hr.leaves.*') ? 'active' : '' }}">
                                            <i class="fas fa-calendar-check fa-fw me-2"></i>
                                            {{ __('messages.leave_management') }}
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        @endcanany
                    @endif

                    @if($checkVisibility('module_transport'))
                        @can('view transport')
                            <!-- Logistics & Transport Section -->
                            <div class="menu-section">{{ __('messages.logistics_transport') }}</div>

                            <div
                                class="menu-item {{ request()->routeIs('logistics.*') || request()->routeIs('transport.*') ? 'open' : '' }}">
                                <button type="button" class="menu-link" data-submenu data-turbo="false">
                                    <i class="fas fa-truck-loading fa-fw"></i>
                                    <span>{{ __('messages.logistics') }}</span>
                                    <i class="fas fa-chevron-down menu-arrow"></i>
                                </button>
                                <div class="submenu">
                                    <a href="{{ route('logistics.vehicles.index') }}"
                                        class="menu-link {{ request()->routeIs('logistics.vehicles.*') ? 'active' : '' }}">
                                        <i class="fas fa-truck fa-fw me-2"></i>
                                        {{ __('messages.delivery_vehicles') }}
                                    </a>
                                    <a href="{{ route('logistics.fuel-logs.index') }}"
                                        class="menu-link {{ request()->routeIs('logistics.fuel-logs.*') ? 'active' : '' }}">
                                        <i class="fas fa-gas-pump fa-fw me-2"></i> {{ __('messages.fuel_logs') }}
                                    </a>
                                    <a href="{{ route('transport.trailers.index') }}"
                                        class="menu-link {{ request()->routeIs('transport.trailers.*') ? 'active' : '' }}">
                                        <i class="fas fa-trailer fa-fw me-2"></i> {{ __('messages.trailers') }}
                                    </a>
                                    <a href="{{ route('transport.orders.index') }}"
                                        class="menu-link {{ request()->routeIs('transport.orders.*') ? 'active' : '' }}">
                                        <i class="fas fa-route fa-fw me-2"></i>
                                        {{ __('messages.transport_orders') }}
                                    </a>
                                    <a href="{{ route('transport.contracts.index') }}"
                                        class="menu-link {{ request()->routeIs('transport.contracts.*') ? 'active' : '' }}">
                                        <i class="fas fa-file-signature fa-fw me-2"></i>
                                        {{ __('messages.contracts') }}
                                    </a>
                                </div>
                            </div>
                        @endcan
                    @endif

                    @if($checkVisibility('module_maintenance'))
                        @can('view maintenance')
                            <!-- Maintenance Section -->
                            <div class="menu-section">{{ __('messages.maintenance') }}</div>

                            @if($checkVisibility('sidebar_workshops'))
                                <div class="menu-item">
                                    <a href="{{ route('maintenance.workshops.index') }}"
                                        class="menu-link {{ request()->routeIs('maintenance.workshops.*') ? 'active' : '' }}">
                                        <i class="fas fa-tools fa-fw me-2"></i> {{ __('messages.workshops') }}
                                    </a>
                                </div>
                            @endif

                            @if($checkVisibility('sidebar_maintenance_vouchers'))
                                <div class="menu-item">
                                    <a href="{{ route('maintenance.vouchers.index') }}"
                                        class="menu-link {{ request()->routeIs('maintenance.vouchers.*') ? 'active' : '' }}">
                                        <i class="fas fa-wrench fa-fw me-2"></i>
                                        {{ __('messages.maintenance_vouchers') }}
                                    </a>
                                </div>
                            @endif
                        @endcan
                    @endif

                    @if($checkVisibility('module_reports'))
                        @can('view reports')
                            <!-- Reports Section -->
                            <div class="menu-section">{{ __('messages.reports') }}</div>

                            @if($checkVisibility('sidebar_sales_reports'))
                                <div class="menu-item {{ request()->routeIs('reports.sales.*') ? 'open' : '' }}">
                                    <button type="button" class="menu-link" data-submenu data-turbo="false">
                                        <i class="fas fa-chart-line fa-fw"></i>
                                        <span>{{ __('messages.sales_reports') }}</span>
                                        <i class="fas fa-chevron-down menu-arrow"></i>
                                    </button>
                                    <div class="submenu">
                                        <a href="{{ route('reports.sales.index') }}"
                                            class="menu-link {{ request()->routeIs('reports.sales.index') ? 'active' : '' }}">
                                            <i class="fas fa-tachometer-alt fa-fw me-2"></i>
                                            {{ __('messages.overview') }}
                                        </a>
                                        <a href="{{ route('reports.sales.by-customer') }}"
                                            class="menu-link {{ request()->routeIs('reports.sales.by-customer') ? 'active' : '' }}">
                                            <i class="fas fa-user-chart fa-fw me-2"></i>
                                            {{ __('messages.reports_by_customer') }}
                                        </a>
                                        <a href="{{ route('reports.sales.by-item') }}"
                                            class="menu-link {{ request()->routeIs('reports.sales.by-item') ? 'active' : '' }}">
                                            <i class="fas fa-box-open fa-fw me-2"></i>
                                            {{ __('messages.reports_by_item') }}
                                        </a>
                                        <a href="{{ route('reports.sales.date-wise') }}"
                                            class="menu-link {{ request()->routeIs('reports.sales.date-wise') ? 'active' : '' }}">
                                            <i class="fas fa-calendar-alt fa-fw me-2"></i>
                                            {{ __('messages.reports_date_wise') }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if($checkVisibility('sidebar_supplier_reports'))
                                <div class="menu-item {{ request()->routeIs('reports.suppliers.*') ? 'open' : '' }}">
                                    <button type="button" class="menu-link" data-submenu data-turbo="false">
                                        <i class="fas fa-truck fa-fw"></i>
                                        <span>{{ __('messages.supplier_reports') }}</span>
                                        <i class="fas fa-chevron-down menu-arrow"></i>
                                    </button>
                                    <div class="submenu">
                                        <a href="{{ route('reports.suppliers.index') }}"
                                            class="menu-link {{ request()->routeIs('reports.suppliers.index') ? 'active' : '' }}">
                                            <i class="fas fa-tachometer-alt fa-fw me-2"></i>
                                            {{ __('messages.overview') }}
                                        </a>
                                        <a href="{{ route('reports.suppliers.by-code-name') }}"
                                            class="menu-link {{ request()->routeIs('reports.suppliers.by-code-name') ? 'active' : '' }}">
                                            <i class="fas fa-address-card fa-fw me-2"></i>
                                            {{ __('messages.reports_by_code_name') }}
                                        </a>
                                        <a href="{{ route('reports.suppliers.local-purchases') }}"
                                            class="menu-link {{ request()->routeIs('reports.suppliers.local-purchases') ? 'active' : '' }}">
                                            <i class="fas fa-store fa-fw me-2"></i> {{ __('messages.local_purchases') }}
                                        </a>
                                        <a href="{{ route('reports.suppliers.purchase-summary') }}"
                                            class="menu-link {{ request()->routeIs('reports.suppliers.purchase-summary') ? 'active' : '' }}">
                                            <i class="fas fa-file-alt fa-fw me-2"></i>
                                            {{ __('messages.reports_purchase_summary') }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if($checkVisibility('sidebar_tax_reports'))
                                <div class="menu-item">
                                    <a href="{{ route('reports.tax.summary') }}"
                                        class="menu-link {{ request()->routeIs('reports.tax.*') ? 'active' : '' }}">
                                        <i class="fas fa-calculator fa-fw"></i>
                                        <span>{{ __('messages.tax_reports') }}</span>
                                    </a>
                                </div>
                            @endif

                            @if($checkVisibility('sidebar_inventory_reports'))
                                <div class="menu-item">
                                    <a href="{{ route('reports.inventory.valuation') }}"
                                        class="menu-link {{ request()->routeIs('reports.inventory.*') ? 'active' : '' }}">
                                        <i class="fas fa-box fa-fw"></i>
                                        <span>{{ __('messages.inventory_reports') }}</span>
                                    </a>
                                </div>
                            @endif
                        @endcan
                    @endif

                    @if($checkVisibility('module_finance'))
                        <!-- Finance & Banking Section -->
                        <div class="menu-section">{{ __('messages.finance_banking') }}</div>
                        <div class="menu-item {{ request()->is('finance*') ? 'open' : '' }}">
                            <button type="button" class="menu-link" data-submenu data-turbo="false">
                                <i class="fas fa-university fa-fw"></i>
                                <span>{{ __('messages.finance_banking') }}</span>
                                <i class="fas fa-chevron-down menu-arrow"></i>
                            </button>
                            <div class="submenu">
                                <a href="{{ route('finance.bank-accounts.index') }}"
                                    class="menu-link {{ request()->routeIs('finance.bank-accounts.*') ? 'active' : '' }}">
                                    <i class="fas fa-university fa-fw me-2"></i>
                                    {{ __('messages.bank_cash_accounts') }}
                                </a>
                                <a href="{{ route('finance.vouchers.payment.index') }}"
                                    class="menu-link {{ request()->routeIs('finance.vouchers.payment.*') ? 'active' : '' }}">
                                    <i class="fas fa-money-check fa-fw me-2"></i>
                                    {{ __('messages.payment_vouchers') }}
                                </a>
                                <a href="{{ route('finance.vouchers.receipt.index') }}"
                                    class="menu-link {{ request()->routeIs('finance.vouchers.receipt.*') ? 'active' : '' }}">
                                    <i class="fas fa-receipt fa-fw me-2"></i>
                                    {{ __('messages.receipt_vouchers') }}
                                </a>
                                <a href="{{ route('finance.budgets.index') }}"
                                    class="menu-link {{ request()->routeIs('finance.budgets.*') ? 'active' : '' }}">
                                    <i class="fas fa-chart-pie fa-fw me-2"></i>
                                    {{ __('messages.budget_management') }}
                                </a>
                                <a href="{{ route('finance.fixed-assets.index') }}"
                                    class="menu-link {{ request()->routeIs('finance.fixed-assets.*') ? 'active' : '' }}">
                                    <i class="fas fa-hard-hat fa-fw me-2"></i> {{ __('messages.fixed_assets') }}
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($checkVisibility('module_accounting'))
                        @can('view accounting')
                            <!-- Accounting System Section -->

                            <!-- General Ledger System -->
                            <div class="menu-item {{ request()->routeIs('accounting.gl.*') ? 'open' : '' }}">
                                <button type="button" class="menu-link" data-submenu data-turbo="false">
                                    <i class="fas fa-book fa-fw"></i>
                                    <span>{{ __('messages.general_ledger_system') }}</span>
                                    <i class="fas fa-chevron-down menu-arrow"></i>
                                </button>
                                <div class="submenu">
                                    @can('manage chart of accounts')
                                        <a href="{{ route('accounting.gl.coa.index') }}"
                                            class="menu-link {{ request()->routeIs('accounting.gl.coa.*') ? 'active' : '' }}">
                                            <i class="fas fa-sitemap fa-fw me-2"></i>
                                            {{ __('messages.chart_of_accounts') }}
                                        </a>
                                    @endcan

                                    <!-- Setup Submenu -->
                                    <div class="menu-item {{ request()->routeIs('accounting.setup.*') ? 'open' : '' }}"
                                        style="margin-left: 10px;">
                                        <button type="button" class="menu-link" data-submenu data-turbo="false">
                                            <i class="fas fa-tools fa-fw"></i>
                                            <span>{{ __('messages.setup') }}</span>
                                            <i class="fas fa-chevron-down menu-arrow"></i>
                                        </button>
                                        <div class="submenu">
                                            @can('view cost_centers')
                                                <a href="{{ route('accounting.gl.setup.cost-centers.index') }}"
                                                    class="menu-link {{ request()->routeIs('accounting.gl.setup.cost-centers.*') ? 'active' : '' }}">
                                                    <i class="fas fa-bullseye fa-fw me-2"></i>
                                                    {{ __('messages.cost_centers') }}
                                                </a>
                                            @endcan
                                            @can('view activities')
                                                <a href="{{ route('accounting.gl.setup.activities.index') }}"
                                                    class="menu-link {{ request()->routeIs('accounting.gl.setup.activities.*') ? 'active' : '' }}">
                                                    <i class="fas fa-tasks fa-fw me-2"></i>
                                                    {{ __('messages.activities') }}
                                                </a>
                                            @endcan
                                            @can('view lcs')
                                                <a href="{{ route('accounting.gl.setup.lcs.index') }}"
                                                    class="menu-link {{ request()->routeIs('accounting.gl.setup.lcs.*') ? 'active' : '' }}">
                                                    <i class="fas fa-file-import fa-fw me-2"></i>
                                                    {{ __('messages.lcs') }}
                                                </a>
                                            @endcan
                                        </div>
                                    </div>

                                    <!-- Transactions Submenu -->
                                    <div class="menu-item {{ request()->routeIs('accounting.gl.transactions.*') ? 'open' : '' }}"
                                        style="margin-left: 10px;">
                                        <button type="button" class="menu-link" data-submenu data-turbo="false">
                                            <i class="fas fa-exchange-alt fa-fw"></i>
                                            <span>{{ __('messages.transactions') }}</span>
                                            <i class="fas fa-chevron-down menu-arrow"></i>
                                        </button>
                                        <div class="submenu">
                                            <a href="{{ route('accounting.gl.transactions.jv.index') }}"
                                                class="menu-link {{ request()->routeIs('accounting.gl.transactions.jv.*') ? 'active' : '' }}">
                                                <i class="fas fa-file-invoice fa-fw me-2"></i>
                                                {{ __('messages.journal_vouchers') }}
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Reports Submenu -->
                                    <div class="menu-item {{ request()->routeIs('accounting.gl.reports.*') ? 'open' : '' }}"
                                        style="margin-left: 10px;">
                                        <button type="button" class="menu-link" data-submenu data-turbo="false">
                                            <i class="fas fa-file-invoice-dollar fa-fw"></i>
                                            <span>{{ __('messages.reports') }}</span>
                                            <i class="fas fa-chevron-down menu-arrow"></i>
                                        </button>
                                        <div class="submenu">
                                            <a href="{{ route('accounting.gl.dashboard') }}"
                                                class="menu-link {{ request()->routeIs('accounting.gl.dashboard') ? 'active' : '' }}">
                                                <i class="fas fa-th-large fa-fw me-2"></i>
                                                {{ __('messages.dashboard') }}
                                            </a>
                                            <a href="{{ route('accounting.gl.reports.account-statement') }}"
                                                class="menu-link {{ request()->routeIs('accounting.gl.reports.account-statement') ? 'active' : '' }}">
                                                <i class="fas fa-file-invoice fa-fw me-2"></i>
                                                {{ __('messages.account_statement_report') }}
                                            </a>
                                            <a href="{{ route('accounting.gl.reports.universal-statement') }}"
                                                class="menu-link {{ request()->routeIs('accounting.gl.reports.universal-statement') ? 'active' : '' }}">
                                                <i class="fas fa-globe fa-fw me-2"></i>
                                                {{ __('messages.universal_statement_report') }}
                                            </a>
                                            <a href="{{ route('accounting.gl.reports.daily-ledger') }}"
                                                class="menu-link {{ request()->routeIs('accounting.gl.reports.daily-ledger') ? 'active' : '' }}">
                                                <i class="fas fa-calendar-day fa-fw me-2"></i>
                                                {{ __('messages.daily_ledger') }}
                                            </a>
                                            <a href="{{ route('accounting.gl.reports.trial-balance') }}"
                                                class="menu-link {{ request()->routeIs('accounting.gl.reports.trial-balance') ? 'active' : '' }}">
                                                <i class="fas fa-balance-scale fa-fw me-2"></i>
                                                {{ __('messages.trial_balance') }}
                                            </a>
                                            <a href="{{ route('accounting.gl.reports.profit-loss') }}"
                                                class="menu-link {{ request()->routeIs('accounting.gl.reports.profit-loss') ? 'active' : '' }}">
                                                <i class="fas fa-chart-line fa-fw me-2"></i>
                                                {{ __('messages.profit_loss') }}
                                            </a>
                                            <a href="{{ route('accounting.gl.reports.balance-sheet') }}"
                                                class="menu-link {{ request()->routeIs('accounting.gl.reports.balance-sheet') ? 'active' : '' }}">
                                                <i class="fas fa-university fa-fw me-2"></i>
                                                {{ __('messages.balance_sheet') }}
                                            </a>
                                            <a href="{{ route('accounting.gl.explorer.index') }}"
                                                class="menu-link {{ request()->routeIs('accounting.gl.explorer.*') ? 'active' : '' }}">
                                                <i class="fas fa-search-plus fa-fw me-2"></i>
                                                {{ __('messages.account_explorer') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endcan
                    @endif

                    @if($checkVisibility('module_healthcare'))
                        <div class="menu-section">{{ __('messages.healthcare_management') }}</div>
                        <div class="menu-item {{ request()->is('healthcare*') ? 'open' : '' }}">
                            <button type="button" class="menu-link" data-submenu data-turbo="false">
                                <i class="fas fa-hospital fa-fw text-danger"></i>
                                <span>{{ __('messages.healthcare_management') }}</span>
                                <i class="fas fa-chevron-down menu-arrow"></i>
                            </button>
                            <div class="submenu">
                                <a href="{{ route('healthcare.patients.index') }}"
                                    class="menu-link {{ request()->routeIs('healthcare.patients.*') ? 'active' : '' }}">
                                    <i class="fas fa-user-injured fa-fw me-2"></i> {{ __('messages.patients') }}
                                </a>
                                <a href="{{ route('healthcare.doctors.index') }}"
                                    class="menu-link {{ request()->routeIs('healthcare.doctors.*') ? 'active' : '' }}">
                                    <i class="fas fa-user-md fa-fw me-2"></i> {{ __('messages.doctors') }}
                                </a>
                                <a href="{{ route('healthcare.appointments.index') }}"
                                    class="menu-link {{ request()->routeIs('healthcare.appointments.*') ? 'active' : '' }}">
                                    <i class="fas fa-calendar-check fa-fw me-2"></i>
                                    {{ __('messages.appointments') }}
                                </a>
                                <a href="{{ route('healthcare.medical-services.index') }}"
                                    class="menu-link {{ request()->routeIs('healthcare.medical-services.*') ? 'active' : '' }}">
                                    <i class="fas fa-stethoscope fa-fw me-2"></i>
                                    {{ __('messages.medical_services') }}
                                </a>
                            </div>
                        </div>
                    @endif
    </nav>
</aside>