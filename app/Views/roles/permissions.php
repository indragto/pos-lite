  <?php
/**
 * Role Permissions View
 * Variables: $title, $role, $permissions, $role_permissions
 */
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1"><i class="fas fa-key me-2"></i>Permissions</h5>
            <p class="text-muted mb-0">Manage permissions for <strong><?= e($role['name']) ?></strong></p>
        </div>
        <a href="<?= url('roles') ?>" class="btn btn-outline-secondary" style="min-height: 44px;">
            <i class="fas fa-arrow-left me-2"></i>Back to Roles
        </a>
    </div>

    <form action="<?= url('roles/permissions/update/' . $role['id']) ?>" method="POST">
        <?= csrf_field() ?>

        <?php
        // Group permissions by module
        $grouped = [];
        foreach ($permissions as $perm) {
            $parts = explode('.', $perm['permission']);
            $module = $parts[0] ?? 'other';
            $grouped[$module][] = $perm;
        }
        ?>

        <div class="row g-3">
            <?php foreach ($grouped as $module => $perms): ?>
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-primary text-white py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-<?= $module === 'transactions' ? 'receipt' : ($module === 'products' ? 'box' : ($module === 'users' ? 'users' : ($module === 'roles' ? 'user-shield' : ($module === 'reports' ? 'chart-bar' : ($module === 'categories' ? 'tags' : ($module === 'settings' ? 'cog' : 'folder'))))))) ?> me-2"></i>
                            <?= ucfirst($module) ?>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php foreach ($perms as $perm): ?>
                            <label class="list-group-item d-flex align-items-center py-3">
                                <input class="form-check-input me-3" type="checkbox" name="permissions[]"
                                       value="<?= e($perm['permission']) ?>"
                                       <?= in_array($perm['permission'], $role_permissions ?? []) ? 'checked' : '' ?>
                                       style="width: 20px; height: 20px;">
                                <div>
                                    <span class="fw-semibold"><?= e(str_replace('_', ' ', ucfirst($perm['permission']))) ?></span>
                                    <br><small class="text-muted"><code><?= e($perm['permission']) ?></code></small>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary flex-grow-1" style="min-height: 48px;">
                <i class="fas fa-save me-2"></i>Save Permissions
            </button>
            <a href="<?= url('roles') ?>" class="btn btn-outline-secondary" style="min-height: 48px;">
                Cancel
            </a>
        </div>
    </form>
</div>