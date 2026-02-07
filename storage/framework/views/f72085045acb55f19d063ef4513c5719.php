

<?php $__env->startSection('title', __('messages.sales_invoices')); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3"><?php echo e(__('messages.sales_invoices')); ?></h1>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create sales invoices')): ?>
                <a href="<?php echo e(route('sales.invoices.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> <?php echo e(__('messages.create')); ?>

                </a>
            <?php endif; ?>
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
                                            <a href="<?php echo e(route('sales.invoices.show', $invoice)); ?>" class="btn btn-sm btn-info"
                                                title="<?php echo e(__('messages.view')); ?>">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if($invoice->isEditable()): ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit sales invoices')): ?>
                                                    <a href="<?php echo e(route('sales.invoices.edit', $invoice)); ?>"
                                                        class="btn btn-sm btn-primary" title="<?php echo e(__('messages.edit')); ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <a href="<?php echo e(route('sales.invoices.pdf', $invoice)); ?>"
                                                class="btn btn-sm btn-secondary" title="<?php echo e(__('messages.download_pdf')); ?>">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
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
                    <?php echo e($invoices->links()); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pc\Downloads\aurex-erp\aurex-erp\resources\views/sales/invoices/index.blade.php ENDPATH**/ ?>