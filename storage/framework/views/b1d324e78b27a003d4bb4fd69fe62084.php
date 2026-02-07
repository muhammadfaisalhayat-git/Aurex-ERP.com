

<?php $__env->startSection('title', __('messages.branches')); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3"><?php echo e(__('messages.branches')); ?></h1>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create branches')): ?>
                <a href="<?php echo e(route('admin.branches.create')); ?>" class="btn btn-primary">
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
                                <th><?php echo e(__('messages.manager')); ?></th>
                                <th><?php echo e(__('messages.status')); ?></th>
                                <th><?php echo e(__('messages.actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($branch->code); ?></td>
                                    <td><?php echo e($branch->name); ?></td>
                                    <td><?php echo e($branch->manager_name ?? '-'); ?></td>
                                    <td>
                                        <?php
                                            $statusClass = $branch->is_active ? 'success' : 'danger';
                                            $statusText = $branch->is_active ? 'active' : 'inactive';
                                        ?>
                                        <span class="badge bg-<?php echo e($statusClass); ?>">
                                            <?php echo e(__('messages.' . $statusText)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo e(route('admin.branches.show', $branch)); ?>" class="btn btn-sm btn-info"
                                                title="<?php echo e(__('messages.view')); ?>">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit branches')): ?>
                                                <a href="<?php echo e(route('admin.branches.edit', $branch)); ?>" class="btn btn-sm btn-primary"
                                                    title="<?php echo e(__('messages.edit')); ?>">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete branches')): ?>
                                                <form action="<?php echo e(route('admin.branches.destroy', $branch)); ?>" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('<?php echo e(__('messages.confirm_delete')); ?>')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        title="<?php echo e(__('messages.delete')); ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center"><?php echo e(__('messages.no_records_found')); ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <?php echo e($branches->links()); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pc\Downloads\aurex-erp\aurex-erp\resources\views/admin/branches/index.blade.php ENDPATH**/ ?>