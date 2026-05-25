<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>Manage permissions for <strong><?= e($role['name']) ?></strong></p>
    </div>
    <a href="<?= url('roles') ?>" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i>Back to Roles
    </a>
</div>

<form action="<?= url("roles/save-permissions/{$role['id']}") ?>" method="POST">
    <?= csrf_field() ?>

    <div class="row g-3 mb-4">
        <?php foreach ($groupedPermissions as $module => $perms): ?>
        <div class="col-md-6 col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h6>
                        <?php
                        $modIcon = match ($module) {
                            'dashboard' => 'fa-th-large',
                            'products' => 'fa-box',
                            'categories' => 'fa-tags',
                            'transactions' => 'fa-receipt',
                            'reports' => 'fa-chart-bar',
                            'users' => 'fa-users',
                            'roles' => 'fa-user-shield',
                            'settings' => 'fa-cog',
                            default => 'fa-folder',
                        };
                        ?>
                        <i class="fas <?= $modIcon ?> text-primary me-2"></i><?= e(ucwords(str_replace('_', ' ', $module))) ?>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <?php foreach ($perms as $perm): ?>
                        <div class="col-12">
                            <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                                <input type="checkbox" name="permissions[]" class="form-check-input"
                                       value="<?= $perm['id'] ?>"
                                       <?= in_array($perm['id'], $rolePermissionIds) ? 'checked' : '' ?>>
                                <span style="font-size:14px;"><?= e(str_replace('_', ' ', ucfirst($perm['name']))) ?></span>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i>Save Permissions
        </button>
        <a href="<?= url('roles') ?>" class="btn btn-ghost">Cancel</a>
    </div>
</form>
