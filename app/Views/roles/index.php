  <?php
/**
 * Roles Index View
 * Variables: $title, $roles
 */
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1"><i class="fas fa-user-shield me-2"></i>Roles</h5>
            <p class="text-muted mb-0">Manage user roles and permissions</p>
        </div>
        <a href="<?= url('roles/create') ?>" class="btn btn-primary" style="min-height: 44px;">
            <i class="fas fa-plus me-2"></i>Add Role
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Role Name</th>
                            <th class="d-none d-md-table-cell">Description</th>
                            <th class="text-center">Users</th>
                            <th class="text-center">Permissions</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($roles)): ?>
                            <?php foreach ($roles as $role): ?>
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 bg-info bg-opacity-10"
                                             style="width: 44px; height: 44px;">
                                            <i class="fas fa-user-shield text-info"></i>
                                        </div>
                                        <div>
                                            <span class="fw-semibold"><?= e($role['name']) ?></span>
                                            <?php if ($role['is_default'] ?? false): ?>
                                            <span class="badge bg-secondary ms-2">Default</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell text-muted"><?= e($role['description'] ?? '-') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark fs-6 px-3 py-2"><?= number_format($role['user_count'] ?? 0) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark fs-6 px-3 py-2"><?= number_format($role['permission_count'] ?? 0) ?></span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group" role="group">
                                        <a href="<?= url("roles/edit/{$role['id']}") ?>"
                                           class="btn btn-sm btn-outline-primary"
                                           style="min-height: 44px; min-width: 44px;" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= url("roles/permissions/{$role['id']}") ?>"
                                           class="btn btn-sm btn-outline-info"
                                           style="min-height: 44px; min-width: 44px;" title="Permissions">
                                            <i class="fas fa-key"></i>
                                        </a>
                                        <?php if (hasPermission('roles.delete') && !($role['is_default'] ?? false)): ?>
                                        <form action="<?= url("roles/delete/{$role['id']}") ?>"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this role?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    style="min-height: 44px; min-width: 44px;" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-user-shield fa-3x mb-3 d-block opacity-50"></i>
                                    <p class="mb-1">No roles found</p>
                                    <small>Create your first role to get started</small>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>