

<?php $__env->startSection('title', __('Branches')); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-header">
        <h1 class="page-title"><?php echo e(__('Branches')); ?></h1>
        <div class="page-actions">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create branches')): ?>
                <a href="<?php echo e(route('admin.branches.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> <?php echo e(__('Add Branch')); ?>

                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th><?php echo e(__('Company')); ?></th>
                            <th><?php echo e(__('Code')); ?></th>
                            <th><?php echo e(__('Name')); ?></th>
                            <th><?php echo e(__('Warehouses')); ?></th>
                            <th><?php echo e(__('Users')); ?></th>
                            <th><?php echo e(__('Status')); ?></th>
                            <th width="150"><?php echo e(__('Actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($branch->id); ?></td>
                                <td>
                                    <div class="fw-bold text-primary"><?php echo e($branch->company->name ?? '-'); ?></div>
                                </td>
                                <td><span class="badge bg-secondary"><?php echo e($branch->code); ?></span></td>
                                <td>
                                    <div class="fw-bold"><?php echo e($branch->name_en); ?></div>
                                    <div class="small text-muted"><?php echo e($branch->name_ar); ?></div>
                                </td>
                                <td><?php echo e($branch->warehouses_count); ?></td>
                                <td><?php echo e($branch->users_count); ?></td>
                                <td>
                                    <?php if($branch->is_active): ?>
                                        <span class="badge bg-success"><?php echo e(__('Active')); ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><?php echo e(__('Inactive')); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?php echo e(route('admin.branches.show', $branch)); ?>"
                                            class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit branches')): ?>
                                            <a href="<?php echo e(route('admin.branches.edit', $branch)); ?>"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete branches')): ?>
                                            <form action="<?php echo e(route('admin.branches.destroy', $branch)); ?>" method="POST"
                                                onsubmit="return confirm('<?php echo e(__('Are you sure?')); ?>')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4"><?php echo e(__('No branches found')); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($branches->hasPages()): ?>
            <div class="card-footer">
                <?php echo e($branches->links()); ?>

            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pc\Downloads\aurex-erp\aurex-erp\resources\views/admin/branches/index.blade.php ENDPATH**/ ?>