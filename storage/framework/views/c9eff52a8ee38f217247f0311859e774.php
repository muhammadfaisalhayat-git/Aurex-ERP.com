

<?php $__env->startSection('title', __('messages.view_customer')); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3"><?php echo e(__('messages.view_customer')); ?>: <?php echo e($customer->name); ?></h1>
            <a href="<?php echo e(route('sales.customers.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> <?php echo e(__('messages.back')); ?>

            </a>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo e($customer->name); ?></h5>
                        <p class="text-muted"><?php echo e($customer->code); ?></p>
                        <div class="mb-3">
                            <?php
                                $statusClass = $customer->status == 'active' ? 'success' : ($customer->status == 'blocked' ? 'danger' : 'secondary');
                            ?>
                            <span class="badge bg-<?php echo e($statusClass); ?>">
                                <?php echo e(__('messages.' . $customer->status)); ?>

                            </span>
                        </div>
                        <p class="mb-1"><strong><?php echo e(__('messages.current_balance')); ?>:</strong>
                            <?php echo e(number_format($customer->current_balance, 2)); ?></p>
                        <p class="mb-1"><strong><?php echo e(__('messages.credit_limit')); ?>:</strong>
                            <?php echo e(number_format($customer->credit_limit, 2)); ?></p>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><?php echo e(__('messages.actions')); ?></div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit customers')): ?>
                                <a href="<?php echo e(route('sales.customers.edit', $customer)); ?>" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> <?php echo e(__('messages.edit_customer')); ?>

                                </a>
                            <?php endif; ?>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete customers')): ?>
                                <form action="<?php echo e(route('sales.customers.destroy', $customer)); ?>" method="POST"
                                    onsubmit="return confirm('<?php echo e(__('messages.confirm_delete')); ?>')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-trash"></i> <?php echo e(__('messages.delete_customer')); ?>

                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <ul class="nav nav-tabs mb-3" id="customerTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details"
                            type="button" role="tab"><?php echo e(__('messages.details')); ?></button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions"
                            type="button" role="tab"><?php echo e(__('messages.transactions')); ?></button>
                    </li>
                </ul>

                <div class="tab-content" id="customerTabsContent">
                    <div class="tab-pane fade show active" id="details" role="tabpanel">
                        <div class="card mb-4">
                            <div class="card-header"><?php echo e(__('messages.basic_information')); ?></div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold"><?php echo e(__('messages.customer_group')); ?></div>
                                    <div class="col-sm-8"><?php echo e($customer->group->name ?? '-'); ?></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold"><?php echo e(__('messages.branch')); ?></div>
                                    <div class="col-sm-8"><?php echo e($customer->branch->name ?? '-'); ?></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold"><?php echo e(__('messages.salesman')); ?></div>
                                    <div class="col-sm-8"><?php echo e($customer->salesman->name ?? '-'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header"><?php echo e(__('messages.contact_information')); ?></div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold"><?php echo e(__('messages.contact_person')); ?></div>
                                    <div class="col-sm-8"><?php echo e($customer->contact_person ?? '-'); ?></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold"><?php echo e(__('messages.phone')); ?></div>
                                    <div class="col-sm-8"><?php echo e($customer->phone ?? '-'); ?></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold"><?php echo e(__('messages.mobile')); ?></div>
                                    <div class="col-sm-8"><?php echo e($customer->mobile ?? '-'); ?></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold"><?php echo e(__('messages.email')); ?></div>
                                    <div class="col-sm-8"><?php echo e($customer->email ?? '-'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header"><?php echo e(__('messages.address_information')); ?></div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold"><?php echo e(__('messages.address')); ?></div>
                                    <div class="col-sm-8"><?php echo e($customer->address ?? '-'); ?></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold"><?php echo e(__('messages.city')); ?></div>
                                    <div class="col-sm-8"><?php echo e($customer->city ?? '-'); ?></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold"><?php echo e(__('messages.region')); ?></div>
                                    <div class="col-sm-8"><?php echo e($customer->region ?? '-'); ?></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold"><?php echo e(__('messages.postal_code')); ?></div>
                                    <div class="col-sm-8"><?php echo e($customer->postal_code ?? '-'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="transactions" role="tabpanel">
                        <div class="card">
                            <div class="card-header"><?php echo e(__('messages.recent_transactions')); ?></div>
                            <div class="card-body">
                                <p class="text-muted text-center pt-3"><?php echo e(__('messages.feature_coming_soon')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pc\Downloads\aurex-erp\aurex-erp\resources\views/sales/customers/show.blade.php ENDPATH**/ ?>