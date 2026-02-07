

<?php $__env->startSection('title', __('messages.view_branch')); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3"><?php echo e(__('messages.view_branch')); ?>: <?php echo e($branch->name); ?></h1>
            <a href="<?php echo e(route('admin.branches.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> <?php echo e(__('messages.back')); ?>

            </a>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo e($branch->name); ?></h5>
                        <p class="text-muted"><?php echo e($branch->code); ?></p>
                        <div>
                            <?php
                                $statusClass = $branch->is_active ? 'success' : 'danger';
                                $statusText = $branch->is_active ? 'active' : 'inactive';
                            ?>
                            <span class="badge bg-<?php echo e($statusClass); ?>">
                                <?php echo e(__('messages.' . $statusText)); ?>

                            </span>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><?php echo e(__('messages.actions')); ?></div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit branches')): ?>
                                <a href="<?php echo e(route('admin.branches.edit', $branch)); ?>" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> <?php echo e(__('messages.edit_branch')); ?>

                                </a>
                            <?php endif; ?>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete branches')): ?>
                                <form action="<?php echo e(route('admin.branches.destroy', $branch)); ?>" method="POST"
                                    onsubmit="return confirm('<?php echo e(__('messages.confirm_delete')); ?>')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-trash"></i> <?php echo e(__('messages.delete_branch')); ?>

                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header"><?php echo e(__('messages.details')); ?></div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold"><?php echo e(__('messages.code')); ?></div>
                            <div class="col-sm-9"><?php echo e($branch->code); ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold"><?php echo e(__('messages.name_en')); ?></div>
                            <div class="col-sm-9"><?php echo e($branch->name_en); ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold"><?php echo e(__('messages.name_ar')); ?></div>
                            <div class="col-sm-9"><?php echo e($branch->name_ar); ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold"><?php echo e(__('messages.phone')); ?></div>
                            <div class="col-sm-9"><?php echo e($branch->phone ?? '-'); ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold"><?php echo e(__('messages.email')); ?></div>
                            <div class="col-sm-9"><?php echo e($branch->email ?? '-'); ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold"><?php echo e(__('messages.manager_name')); ?></div>
                            <div class="col-sm-9"><?php echo e($branch->manager_name ?? '-'); ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold"><?php echo e(__('messages.address')); ?></div>
                            <div class="col-sm-9"><?php echo e($branch->address ?? '-'); ?></div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><?php echo e(__('messages.users')); ?></div>
                    <div class="card-body">
                        <?php if($branch->users->count() > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th><?php echo e(__('messages.name')); ?></th>
                                            <th><?php echo e(__('messages.email')); ?></th>
                                            <th><?php echo e(__('messages.role')); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $branch->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <a href="<?php echo e(route('admin.users.show', $user)); ?>"><?php echo e($user->name); ?></a>
                                                </td>
                                                <td><?php echo e($user->email); ?></td>
                                                <td>
                                                    <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <span class="badge bg-secondary"><?php echo e($role->name); ?></span>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-center text-muted m-0"><?php echo e(__('messages.no_users_assigned')); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pc\Downloads\aurex-erp\aurex-erp\resources\views/admin/branches/show.blade.php ENDPATH**/ ?>