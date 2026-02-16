<aside class="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-brand">
            <i class="fas fa-cube"></i>
            <span><?php echo e(__('messages.app_name')); ?></span>
        </a>
    </div>

    <nav class="sidebar-menu">
        <!-- Dashboard -->
        <div class="menu-item">
            <a href="<?php echo e(route('dashboard')); ?>" class="menu-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                <i class="fas fa-tachometer-alt fa-fw"></i>
                <span><?php echo e(__('messages.dashboard')); ?></span>
            </a>
        </div>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view users', 'manage roles', 'manage settings'])): ?>
            <!-- Admin Section -->
            <div class="menu-section"><?php echo e(__('messages.administration')); ?></div>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view users', 'manage roles'])): ?>
                <div
                    class="menu-item <?php echo e(request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') ? 'open' : ''); ?>">
                    <a href="#" class="menu-link" data-submenu>
                        <i class="fas fa-users-cog fa-fw"></i>
                        <span><?php echo e(__('messages.user_management')); ?></span>
                        <i class="fas fa-chevron-down menu-arrow"></i>
                    </a>
                    <div class="submenu">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view users')): ?>
                            <a href="<?php echo e(route('admin.users.index')); ?>"
                                class="menu-link <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">
                                <?php echo e(__('messages.users')); ?>

                            </a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage roles')): ?>
                            <a href="<?php echo e(route('admin.roles.index')); ?>"
                                class="menu-link <?php echo e(request()->routeIs('admin.roles.*') ? 'active' : ''); ?>">
                                <?php echo e(__('messages.roles')); ?>

                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view users'])): ?>
                <div
                    class="menu-item <?php echo e(request()->routeIs('admin.companies.*') || request()->routeIs('admin.branches.*') || request()->routeIs('admin.warehouses.*') ? 'open' : ''); ?>">
                    <a href="#" class="menu-link" data-submenu>
                        <i class="fas fa-building fa-fw"></i>
                        <span><?php echo e(__('messages.organization')); ?></span>
                        <i class="fas fa-chevron-down menu-arrow"></i>
                    </a>
                    <div class="submenu">
                        <?php if(auth()->user()->hasRole('Super Admin')): ?>
                            <a href="<?php echo e(route('admin.companies.index')); ?>"
                                class="menu-link <?php echo e(request()->routeIs('admin.companies.*') ? 'active' : ''); ?>">
                                <?php echo e(__('messages.companies')); ?>

                            </a>
                        <?php endif; ?>
                        <a href="<?php echo e(route('admin.branches.index')); ?>"
                            class="menu-link <?php echo e(request()->routeIs('admin.branches.*') ? 'active' : ''); ?>">
                            <?php echo e(__('messages.branches')); ?>

                        </a>
                        <a href="<?php echo e(route('admin.warehouses.index')); ?>"
                            class="menu-link <?php echo e(request()->routeIs('admin.warehouses.*') ? 'active' : ''); ?>">
                            <?php echo e(__('messages.warehouses')); ?>

                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage settings')): ?>
                <div class="menu-item">
                    <a href="<?php echo e(route('admin.settings.index')); ?>"
                        class="menu-link <?php echo e(request()->routeIs('admin.settings.*') ? 'active' : ''); ?>">
                        <i class="fas fa-cog fa-fw"></i>
                        <span><?php echo e(__('messages.settings')); ?></span>
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view customers', 'view quotations', 'view invoices', 'view returns', 'view commissions', 'view customer_registration'])): ?>
            <!-- Sales Section -->
            <div class="menu-section"><?php echo e(__('messages.sales')); ?></div>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view customers')): ?>
                <div class="menu-item">
                    <a href="<?php echo e(route('sales.customers.index')); ?>"
                        class="menu-link <?php echo e(request()->routeIs('sales.customers.*') ? 'active' : ''); ?>">
                        <i class="fas fa-users fa-fw"></i>
                        <span><?php echo e(__('messages.customers')); ?></span>
                    </a>
                </div>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view quotations', 'view customer_registration'])): ?>
                <div
                    class="menu-item <?php echo e(request()->routeIs('sales.customer-registrations.*') || request()->routeIs('sales.customer-requests.*') || request()->routeIs('sales.quotations.*') || request()->routeIs('sales.contracts.*') || request()->routeIs('sales.sales-orders.*') ? 'open' : ''); ?>">
                    <a href="#" class="menu-link" data-submenu>
                        <i class="fas fa-file-invoice fa-fw"></i>
                        <span><?php echo e(__('messages.sales_documents')); ?></span>
                        <i class="fas fa-chevron-down menu-arrow"></i>
                    </a>
                    <div class="submenu">
                        <a href="<?php echo e(route('sales.customer-requests.index')); ?>"
                            class="menu-link <?php echo e(request()->routeIs('sales.customer-requests.*') ? 'active' : ''); ?>">
                            <?php echo e(__('messages.customer_requests')); ?>

                        </a>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view quotations')): ?>
                            <a href="<?php echo e(route('sales.quotations.index')); ?>"
                                class="menu-link <?php echo e(request()->routeIs('sales.quotations.*') ? 'active' : ''); ?>">
                                <?php echo e(__('messages.quotations')); ?>

                            </a>
                        <?php endif; ?>
                        <a href="<?php echo e(route('sales.sales-orders.index')); ?>"
                            class="menu-link <?php echo e(request()->routeIs('sales.sales-orders.*') ? 'active' : ''); ?>">
                            <?php echo e(__('messages.sales_orders')); ?>

                        </a>
                        <a href="<?php echo e(route('sales.contracts.index')); ?>"
                            class="menu-link <?php echo e(request()->routeIs('sales.contracts.*') ? 'active' : ''); ?>">
                            <?php echo e(__('messages.sales_contracts')); ?>

                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view invoices')): ?>
                <div class="menu-item">
                    <a href="<?php echo e(route('sales.invoices.index')); ?>"
                        class="menu-link <?php echo e(request()->routeIs('sales.invoices.*') ? 'active' : ''); ?>">
                        <i class="fas fa-file-invoice-dollar fa-fw"></i>
                        <span><?php echo e(__('messages.sales_invoices')); ?></span>
                    </a>
                </div>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view returns')): ?>
                <div class="menu-item">
                    <a href="<?php echo e(route('sales.returns.index')); ?>"
                        class="menu-link <?php echo e(request()->routeIs('sales.returns.*') ? 'active' : ''); ?>">
                        <i class="fas fa-undo fa-fw"></i>
                        <span><?php echo e(__('messages.sales_returns')); ?></span>
                    </a>
                </div>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view commissions')): ?>
                <div class="menu-item">
                    <a href="<?php echo e(route('sales.commissions.rules')); ?>"
                        class="menu-link <?php echo e(request()->routeIs('sales.commissions.*') ? 'active' : ''); ?>">
                        <i class="fas fa-percentage fa-fw"></i>
                        <span><?php echo e(__('messages.commissions')); ?></span>
                    </a>
                </div>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view customer_registration')): ?>
                <div class="menu-item">
                    <a href="<?php echo e(route('sales.customer-registrations.index')); ?>"
                        class="menu-link <?php echo e(request()->routeIs('sales.customer-registrations.*') ? 'active' : ''); ?>">
                        <i class="fas fa-user-plus fa-fw"></i>
                        <span><?php echo e(__('messages.customer_registrations')); ?></span>
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view vendors', 'view purchases', 'view local_purchase', 'view supplier_registration'])): ?>
            <!-- Purchases Section -->
            <div class="menu-section"><?php echo e(__('messages.purchases')); ?></div>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view vendors')): ?>
                <div class="menu-item">
                    <a href="<?php echo e(route('purchases.vendors.index')); ?>"
                        class="menu-link <?php echo e(request()->routeIs('purchases.vendors.*') ? 'active' : ''); ?>">
                        <i class="fas fa-truck fa-fw"></i>
                        <span><?php echo e(__('messages.vendors')); ?></span>
                    </a>
                </div>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view purchases')): ?>
                <div class="menu-item">
                    <a href="<?php echo e(route('purchases.supply-orders.index')); ?>"
                        class="menu-link <?php echo e(request()->routeIs('purchases.supply-orders.*') ? 'active' : ''); ?>">
                        <i class="fas fa-clipboard-list fa-fw"></i>
                        <span><?php echo e(__('messages.supply_orders')); ?></span>
                    </a>
                </div>

                <div class="menu-item">
                    <a href="<?php echo e(route('purchases.invoices.index')); ?>"
                        class="menu-link <?php echo e(request()->routeIs('purchases.invoices.*') ? 'active' : ''); ?>">
                        <i class="fas fa-shopping-cart fa-fw"></i>
                        <span><?php echo e(__('messages.purchase_invoices')); ?></span>
                    </a>
                </div>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view local_purchase')): ?>
                <div class="menu-item">
                    <a href="<?php echo e(route('purchases.local-purchases.index')); ?>"
                        class="menu-link <?php echo e(request()->routeIs('purchases.local-purchases.*') ? 'active' : ''); ?>">
                        <i class="fas fa-store fa-fw"></i>
                        <span><?php echo e(__('messages.local_purchases')); ?></span>
                    </a>
                </div>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view supplier_registration')): ?>
                <div class="menu-item">
                    <a href="<?php echo e(route('purchases.supplier-registrations.index')); ?>"
                        class="menu-link <?php echo e(request()->routeIs('purchases.supplier-registrations.*') ? 'active' : ''); ?>">
                        <i class="fas fa-user-plus fa-fw"></i>
                        <span><?php echo e(__('messages.supplier_registrations')); ?></span>
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view products', 'view inventory'])): ?>
            <!-- Inventory Section -->
            <div class="menu-section"><?php echo e(__('messages.inventory')); ?></div>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view products')): ?>
                <div
                    class="menu-item <?php echo e(request()->routeIs('inventory.products.*') || request()->routeIs('inventory.categories.*') ? 'open' : ''); ?>">
                    <a href="#" class="menu-link" data-submenu>
                        <i class="fas fa-boxes fa-fw"></i>
                        <span><?php echo e(__('messages.products')); ?></span>
                        <i class="fas fa-chevron-down menu-arrow"></i>
                    </a>
                    <div class="submenu">
                        <a href="<?php echo e(route('inventory.products.index')); ?>"
                            class="menu-link <?php echo e(request()->routeIs('inventory.products.*') ? 'active' : ''); ?>">
                            <?php echo e(__('messages.products')); ?>

                        </a>
                        <a href="<?php echo e(route('inventory.categories.index')); ?>"
                            class="menu-link <?php echo e(request()->routeIs('inventory.categories.*') ? 'active' : ''); ?>">
                            <?php echo e(__('messages.categories')); ?>

                        </a>
                        <a href="<?php echo e(route('inventory.barcodes.index')); ?>"
                            class="menu-link <?php echo e(request()->routeIs('inventory.barcodes.*') ? 'active' : ''); ?>">
                            <?php echo e(__('messages.barcode_generator')); ?>

                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view inventory')): ?>
                <div
                    class="menu-item <?php echo e(request()->routeIs('inventory.stock-supply.*') || request()->routeIs('inventory.stock-receiving.*') || request()->routeIs('inventory.stock-transfers.*') ? 'open' : ''); ?>">
                    <a href="#" class="menu-link" data-submenu>
                        <i class="fas fa-warehouse fa-fw"></i>
                        <span><?php echo e(__('messages.stock_management')); ?></span>
                        <i class="fas fa-chevron-down menu-arrow"></i>
                    </a>
                    <div class="submenu">
                        <a href="<?php echo e(route('inventory.stock-supply.index')); ?>"
                            class="menu-link <?php echo e(request()->routeIs('inventory.stock-supply.*') ? 'active' : ''); ?>">
                            <?php echo e(__('messages.stock_supply')); ?>

                        </a>
                        <a href="<?php echo e(route('inventory.stock-receiving.index')); ?>"
                            class="menu-link <?php echo e(request()->routeIs('inventory.stock-receiving.*') ? 'active' : ''); ?>">
                            <?php echo e(__('messages.stock_receiving')); ?>

                        </a>
                        <a href="<?php echo e(route('inventory.stock-transfers.index')); ?>"
                            class="menu-link <?php echo e(request()->routeIs('inventory.stock-transfers.*') ? 'active' : ''); ?>">
                            <?php echo e(__('messages.stock_transfers')); ?>

                        </a>
                        <a href="<?php echo e(route('inventory.transfer-requests.index')); ?>"
                            class="menu-link <?php echo e(request()->routeIs('inventory.transfer-requests.*') ? 'active' : ''); ?>">
                            <?php echo e(__('messages.transfer_requests')); ?>

                        </a>
                        <a href="<?php echo e(route('inventory.issue-orders.index')); ?>"
                            class="menu-link <?php echo e(request()->routeIs('inventory.issue-orders.*') ? 'active' : ''); ?>">
                            <?php echo e(__('messages.issue_orders')); ?>

                        </a>
                        <a href="<?php echo e(route('inventory.assemblies.index')); ?>"
                            class="menu-link <?php echo e(request()->routeIs('inventory.assemblies.*') ? 'active' : ''); ?>">
                            <?php echo e(__('messages.composite_assemblies')); ?>

                        </a>
                    </div>
                </div>

                <div class="menu-item">
                    <a href="<?php echo e(route('inventory.stock-ledger.index')); ?>"
                        class="menu-link <?php echo e(request()->routeIs('inventory.stock-ledger.*') ? 'active' : ''); ?>">
                        <i class="fas fa-list-alt fa-fw"></i>
                        <span><?php echo e(__('messages.stock_ledger')); ?></span>
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view employees', 'view departments', 'view designations'])): ?>
            <!-- Human Resources Section -->
            <div class="menu-section"><?php echo e(__('messages.human_resources')); ?></div>

            <div
                class="menu-item <?php echo e(request()->routeIs('hr.*') || request()->routeIs('hr.employees.*') || request()->routeIs('hr.departments.*') || request()->routeIs('hr.designations.*') ? 'open' : ''); ?>">
                <a href="#" class="menu-link" data-submenu>
                    <i class="fas fa-users fa-fw"></i>
                    <span><?php echo e(__('messages.human_resources')); ?></span>
                    <i class="fas fa-chevron-down menu-arrow"></i>
                </a>
                <div class="submenu">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view employees')): ?>
                        <a href="<?php echo e(route('hr.employees.index')); ?>"
                            class="menu-link <?php echo e(request()->routeIs('hr.employees.*') ? 'active' : ''); ?>">
                            <?php echo e(__('messages.employees')); ?>

                        </a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view departments')): ?>
                        <a href="<?php echo e(route('hr.departments.index')); ?>"
                            class="menu-link <?php echo e(request()->routeIs('hr.departments.*') ? 'active' : ''); ?>">
                            <?php echo e(__('messages.departments')); ?>

                        </a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view designations')): ?>
                        <a href="<?php echo e(route('hr.designations.index')); ?>"
                            class="menu-link <?php echo e(request()->routeIs('hr.designations.*') ? 'active' : ''); ?>">
                            <?php echo e(__('messages.designations')); ?>

                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view transport')): ?>
            <!-- Transport Section -->
            <div class="menu-section"><?php echo e(__('messages.transport')); ?></div>

            <div class="menu-item">
                <a href="<?php echo e(route('transport.trailers.index')); ?>"
                    class="menu-link <?php echo e(request()->routeIs('transport.trailers.*') ? 'active' : ''); ?>">
                    <i class="fas fa-truck-moving fa-fw"></i>
                    <span><?php echo e(__('messages.trailers')); ?></span>
                </a>
            </div>

            <div class="menu-item">
                <a href="<?php echo e(route('transport.orders.index')); ?>"
                    class="menu-link <?php echo e(request()->routeIs('transport.orders.*') ? 'active' : ''); ?>">
                    <i class="fas fa-shipping-fast fa-fw"></i>
                    <span><?php echo e(__('messages.transport_orders')); ?></span>
                </a>
            </div>

            <div class="menu-item">
                <a href="<?php echo e(route('transport.contracts.index')); ?>"
                    class="menu-link <?php echo e(request()->routeIs('transport.contracts.*') ? 'active' : ''); ?>">
                    <i class="fas fa-file-contract fa-fw"></i>
                    <span><?php echo e(__('messages.transport_contracts')); ?></span>
                </a>
            </div>

            <div class="menu-item">
                <a href="<?php echo e(route('transport.claims.index')); ?>"
                    class="menu-link <?php echo e(request()->routeIs('transport.claims.*') ? 'active' : ''); ?>">
                    <i class="fas fa-exclamation-triangle fa-fw"></i>
                    <span><?php echo e(__('messages.transport_claims')); ?></span>
                </a>
            </div>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view maintenance')): ?>
            <!-- Maintenance Section -->
            <div class="menu-section"><?php echo e(__('messages.maintenance')); ?></div>

            <div class="menu-item">
                <a href="<?php echo e(route('maintenance.workshops.index')); ?>"
                    class="menu-link <?php echo e(request()->routeIs('maintenance.workshops.*') ? 'active' : ''); ?>">
                    <i class="fas fa-tools fa-fw"></i>
                    <span><?php echo e(__('messages.workshops')); ?></span>
                </a>
            </div>

            <div class="menu-item">
                <a href="<?php echo e(route('maintenance.vouchers.index')); ?>"
                    class="menu-link <?php echo e(request()->routeIs('maintenance.vouchers.*') ? 'active' : ''); ?>">
                    <i class="fas fa-wrench fa-fw"></i>
                    <span><?php echo e(__('messages.maintenance_vouchers')); ?></span>
                </a>
            </div>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view reports')): ?>
            <!-- Reports Section -->
            <div class="menu-section"><?php echo e(__('messages.reports')); ?></div>

            <div class="menu-item <?php echo e(request()->routeIs('reports.sales.*') ? 'open' : ''); ?>">
                <a href="#" class="menu-link" data-submenu>
                    <i class="fas fa-chart-line fa-fw"></i>
                    <span><?php echo e(__('messages.sales_reports')); ?></span>
                    <i class="fas fa-chevron-down menu-arrow"></i>
                </a>
                <div class="submenu">
                    <a href="<?php echo e(route('reports.sales.index')); ?>"
                        class="menu-link <?php echo e(request()->routeIs('reports.sales.index') ? 'active' : ''); ?>">
                        <?php echo e(__('messages.overview')); ?>

                    </a>
                    <a href="<?php echo e(route('reports.sales.by-customer')); ?>"
                        class="menu-link <?php echo e(request()->routeIs('reports.sales.by-customer') ? 'active' : ''); ?>">
                        <?php echo e(__('messages.reports_by_customer')); ?>

                    </a>
                    <a href="<?php echo e(route('reports.sales.by-item')); ?>"
                        class="menu-link <?php echo e(request()->routeIs('reports.sales.by-item') ? 'active' : ''); ?>">
                        <?php echo e(__('messages.reports_by_item')); ?>

                    </a>
                    <a href="<?php echo e(route('reports.sales.date-wise')); ?>"
                        class="menu-link <?php echo e(request()->routeIs('reports.sales.date-wise') ? 'active' : ''); ?>">
                        <?php echo e(__('messages.reports_date_wise')); ?>

                    </a>
                </div>
            </div>

            <div class="menu-item <?php echo e(request()->routeIs('reports.suppliers.*') ? 'open' : ''); ?>">
                <a href="#" class="menu-link" data-submenu>
                    <i class="fas fa-truck fa-fw"></i>
                    <span><?php echo e(__('messages.supplier_reports')); ?></span>
                    <i class="fas fa-chevron-down menu-arrow"></i>
                </a>
                <div class="submenu">
                    <a href="<?php echo e(route('reports.suppliers.index')); ?>"
                        class="menu-link <?php echo e(request()->routeIs('reports.suppliers.index') ? 'active' : ''); ?>">
                        <?php echo e(__('messages.overview')); ?>

                    </a>
                    <a href="<?php echo e(route('reports.suppliers.by-code-name')); ?>"
                        class="menu-link <?php echo e(request()->routeIs('reports.suppliers.by-code-name') ? 'active' : ''); ?>">
                        <?php echo e(__('messages.reports_by_code_name')); ?>

                    </a>
                    <a href="<?php echo e(route('reports.suppliers.local-purchases')); ?>"
                        class="menu-link <?php echo e(request()->routeIs('reports.suppliers.local-purchases') ? 'active' : ''); ?>">
                        <?php echo e(__('messages.local_purchases')); ?>

                    </a>
                    <a href="<?php echo e(route('reports.suppliers.purchase-summary')); ?>"
                        class="menu-link <?php echo e(request()->routeIs('reports.suppliers.purchase-summary') ? 'active' : ''); ?>">
                        <?php echo e(__('messages.reports_purchase_summary')); ?>

                    </a>
                </div>
            </div>

            <div class="menu-item">
                <a href="<?php echo e(route('reports.tax.summary')); ?>"
                    class="menu-link <?php echo e(request()->routeIs('reports.tax.*') ? 'active' : ''); ?>">
                    <i class="fas fa-calculator fa-fw"></i>
                    <span><?php echo e(__('messages.tax_reports')); ?></span>
                </a>
            </div>

            <div class="menu-item">
                <a href="<?php echo e(route('reports.inventory.valuation')); ?>"
                    class="menu-link <?php echo e(request()->routeIs('reports.inventory.*') ? 'active' : ''); ?>">
                    <i class="fas fa-box fa-fw"></i>
                    <span><?php echo e(__('messages.inventory_reports')); ?></span>
                </a>
            </div>
        <?php endif; ?>

    </nav>
</aside><?php /**PATH C:\Users\Pc\Downloads\aurex-erp\aurex-erp\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>