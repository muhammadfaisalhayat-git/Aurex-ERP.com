

<?php $__env->startSection('title', __('messages.sales_invoices')); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3"><?php echo e(__('messages.sales_invoices')); ?></h1>
            <div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create invoices')): ?>
                    <a href="<?php echo e(route('inventory.barcodes.index')); ?>" class="btn btn-outline-primary me-2">
                        <i class="fas fa-barcode me-2"></i> <?php echo e(__('messages.barcode_generator')); ?>

                    </a>
                    <a href="<?php echo e(route('sales.invoices.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i> <?php echo e(__('messages.create')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mb-4 glassy">
            <div class="card-body">
                <form action="<?php echo e(route('sales.invoices.index')); ?>" method="GET" class="row g-4">
                    
                    <div class="col-md-4">
                        <label for="invoice_number" class="form-label fw-bold"><?php echo e(__('messages.invoice_number')); ?></label>
                        <div class="input-group">
                            <input type="text" name="invoice_number" id="invoice_number" class="form-control bg-white"
                                value="<?php echo e(request('invoice_number')); ?>" placeholder="Search Invoice #">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="customer_search" class="form-label fw-bold"><?php echo e(__('messages.customer')); ?></label>
                        <div class="position-relative">
                            <input type="text" id="customer_search" name="customer_name"
                                class="form-control bg-white shadow-none" placeholder="<?php echo e(__('messages.all_customers')); ?>"
                                value="<?php echo e(request('customer_id') ? ($customers->find(request('customer_id'))->name_en ?? '') : request('customer_name')); ?>"
                                autocomplete="off">
                            <input type="hidden" name="customer_id" id="customer_id" value="<?php echo e(request('customer_id')); ?>">
                            <div id="customer-results" class="search-results-container glassy" style="display: none;">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label fw-bold"><?php echo e(__('messages.status')); ?></label>
                        <select name="status" id="status" class="form-select bg-white shadow-none">
                            <option value=""><?php echo e(__('messages.all_statuses')); ?></option>
                            <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>
                                <?php echo e(__('messages.draft')); ?>

                            </option>
                            <option value="posted" <?php echo e(request('status') == 'posted' ? 'selected' : ''); ?>>
                                <?php echo e(__('messages.posted')); ?>

                            </option>
                            <option value="paid" <?php echo e(request('status') == 'paid' ? 'selected' : ''); ?>><?php echo e(__('messages.paid')); ?>

                            </option>
                            <option value="partial" <?php echo e(request('status') == 'partial' ? 'selected' : ''); ?>>
                                <?php echo e(__('messages.partial')); ?>

                            </option>
                        </select>
                    </div>

                    
                    <div class="col-md-3">
                        <label for="date_from" class="form-label fw-bold"><?php echo e(__('messages.date_from')); ?></label>
                        <input type="date" name="date_from" id="date_from" class="form-control bg-white shadow-none"
                            value="<?php echo e(request('date_from')); ?>" placeholder="mm/dd/yyyy">
                    </div>

                    <div class="col-md-3">
                        <label for="date_to" class="form-label fw-bold"><?php echo e(__('messages.date_to')); ?></label>
                        <input type="date" name="date_to" id="date_to" class="form-control bg-white shadow-none"
                            value="<?php echo e(request('date_to')); ?>" placeholder="mm/dd/yyyy">
                    </div>

                    <div class="col-md-3">
                        <label for="total" class="form-label fw-bold"><?php echo e(__('messages.total')); ?></label>
                        <input type="number" name="total" id="total" class="form-control bg-white shadow-none" step="0.01"
                            value="<?php echo e(request('total')); ?>" placeholder="0.00">
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> <?php echo e(__('messages.search')); ?>

                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th><?php echo e(__('messages.invoice_number')); ?></th>
                                <th><?php echo e(__('messages.date')); ?></th>
                                <th><?php echo e(__('messages.customer')); ?></th>
                                <th><?php echo e(__('messages.total')); ?></th>
                                <th><?php echo e(__('messages.status')); ?></th>
                                <th><?php echo e(__('messages.actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo e(route('sales.invoices.show', $invoice)); ?>">
                                            <?php echo e($invoice->invoice_number); ?>

                                        </a>
                                        <br>
                                        <small class="text-muted"><?php echo e($invoice->document_number); ?></small>
                                    </td>
                                    <td><?php echo e($invoice->invoice_date->format('Y-m-d')); ?></td>
                                    <td><?php echo e($invoice->customer->name ?? '-'); ?></td>
                                    <td><?php echo e(number_format($invoice->total_amount, 2)); ?></td>
                                    <td>
                                        <?php
                                            $statusClass = [
                                                'draft' => 'secondary',
                                                'posted' => 'info',
                                                'paid' => 'success',
                                                'partial' => 'primary',
                                                'overdue' => 'danger',
                                                'cancelled' => 'dark',
                                            ][$invoice->status] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo e($statusClass); ?>">
                                            <?php echo e(__('messages.' . $invoice->status)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view invoices')): ?>
                                                <a href="<?php echo e(route('sales.invoices.show', $invoice)); ?>" class="btn btn-sm btn-info"
                                                    title="<?php echo e(__('messages.view')); ?>">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if($invoice->isEditable()): ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit invoices')): ?>
                                                    <a href="<?php echo e(route('sales.invoices.edit', $invoice)); ?>"
                                                        class="btn btn-sm btn-primary" title="<?php echo e(__('messages.edit')); ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view invoices')): ?>
                                                <a href="<?php echo e(route('sales.invoices.pdf', $invoice)); ?>"
                                                    class="btn btn-sm btn-secondary" title="<?php echo e(__('messages.download_pdf')); ?>">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center"><?php echo e(__('messages.no_records_found')); ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <?php echo e($invoices->appends(request()->query())->links()); ?>

                </div>
            </div>
        </div>
    </div>
    <?php $__env->startPush('scripts'); ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const customerSearch = document.getElementById('customer_search');
                const customerId = document.getElementById('customer_id');
                const customerResults = document.getElementById('customer-results');
                const customerData = <?php echo json_encode($customers->map(function ($c) {
                    return ['id' => $c->id, 'name' => $c->name_en, 'code' => $c->code];
                })) ?>;

                const invoiceNumberSearch = document.getElementById('invoice_number');

                // F2 Shortcut
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'F2') {
                        e.preventDefault();
                        invoiceNumberSearch.focus();
                        invoiceNumberSearch.select();
                    }
                });

                function performSearch(val) {
                    const search = val.toLowerCase();
                    const filtered = customerData.filter(c =>
                        c.name.toLowerCase().includes(search) ||
                        (c.code && c.code.toLowerCase().includes(search))
                    );

                    renderResults(customerResults, filtered, (customer) => {
                        customerSearch.value = customer.name;
                        customerId.value = customer.id;
                        customerResults.style.display = 'none';
                    });
                }

                // Searchable Customer Dropdown
                customerSearch.addEventListener('focus', function () {
                    performSearch(this.value);
                });

                customerSearch.addEventListener('input', function () {
                    if (this.value.length < 1) {
                        customerId.value = '';
                    }
                    performSearch(this.value);
                });

                function renderResults(container, data, onSelect) {
                    if (data.length === 0) {
                        container.style.display = 'none';
                        return;
                    }

                    container.innerHTML = data.map(item => `
                                                                                <div class="search-result-item" data-id="${item.id}">
                                                                                    <div class="item-title">${item.name}</div>
                                                                                    ${item.code ? `<div class="item-subtitle">${item.code}</div>` : ''}
                                                                                </div>
                                                                            `).join('');

                    container.style.display = 'block';

                    container.querySelectorAll('.search-result-item').forEach((el, index) => {
                        el.addEventListener('click', () => {
                            const item = data[index];
                            onSelect(item);
                        });
                    });
                }

                // Close dropdown when clicking outside
                document.addEventListener('click', function (e) {
                    if (!customerSearch.contains(e.target) && !customerResults.contains(e.target)) {
                        customerResults.style.display = 'none';
                    }
                });

                // Keyboard navigation for dropdown
                customerSearch.addEventListener('keydown', function (e) {
                    const items = customerResults.querySelectorAll('.search-result-item');
                    let activeIndex = Array.from(items).findIndex(i => i.classList.contains('active'));

                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        if (activeIndex < items.length - 1) {
                            if (activeIndex > -1) items[activeIndex].classList.remove('active');
                            items[++activeIndex].classList.add('active');
                            items[activeIndex].scrollIntoView({ block: 'nearest' });
                        }
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        if (activeIndex > 0) {
                            items[activeIndex].classList.remove('active');
                            items[--activeIndex].classList.add('active');
                            items[activeIndex].scrollIntoView({ block: 'nearest' });
                        }
                    } else if (e.key === 'Enter' && activeIndex > -1) {
                        e.preventDefault();
                        items[activeIndex].click();
                    } else if (e.key === 'Escape') {
                        customerResults.style.display = 'none';
                    }
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pc\Downloads\aurex-erp\aurex-erp\resources\views/sales/invoices/index.blade.php ENDPATH**/ ?>