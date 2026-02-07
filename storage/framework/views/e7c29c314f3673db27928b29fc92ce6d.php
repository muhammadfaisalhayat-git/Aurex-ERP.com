

<?php $__env->startSection('title', __('messages.customers')); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3"><?php echo e(__('messages.customers')); ?></h1>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create customers')): ?>
                <a href="<?php echo e(route('sales.customers.create')); ?>" class="btn btn-primary">
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
                                <th><?php echo e(__('messages.code')); ?></th>
                                <th><?php echo e(__('messages.name')); ?></th>
                                <th><?php echo e(__('messages.email')); ?></th>
                                <th><?php echo e(__('messages.phone')); ?></th>
                                <th><?php echo e(__('messages.balance')); ?></th>
                                <th><?php echo e(__('messages.status')); ?></th>
                                <th><?php echo e(__('messages.actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo e(route('sales.customers.show', $customer)); ?>">
                                            <?php echo e($customer->code); ?>

                                        </a>
                                    </td>
                                    <td><?php echo e($customer->name); ?></td>
                                    <td><?php echo e($customer->email); ?></td>
                                    <td><?php echo e($customer->phone); ?></td>
                                    <td><?php echo e(number_format($customer->current_balance, 2)); ?></td>
                                    <td>
                                        <?php
                                            $statusClass = $customer->status === 'active' ? 'success' : 'danger';
                                        ?>
                                        <span class="badge bg-<?php echo e($statusClass); ?>">
                                            <?php echo e(__('messages.' . $customer->status)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo e(route('sales.customers.show', $customer)); ?>" class="btn btn-sm btn-info"
                                                title="<?php echo e(__('messages.view')); ?>">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit customers')): ?>
                                                <a href="<?php echo e(route('sales.customers.edit', $customer)); ?>"
                                                    class="btn btn-sm btn-primary" title="<?php echo e(__('messages.edit')); ?>">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?php echo e(route('sales.customers.statement', $customer)); ?>"
                                                class="btn btn-sm btn-secondary" title="<?php echo e(__('messages.view_statement')); ?>">
                                                <i class="fas fa-file-invoice-dollar"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center"><?php echo e(__('messages.no_records_found')); ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <?php echo e($customers->links()); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pc\Downloads\aurex-erp\aurex-erp\resources\views/sales/customers/index.blade.php ENDPATH**/ ?>