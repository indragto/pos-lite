<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>Manage user roles and permissions</p>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th class="text-center">Permissions</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($roles)): ?>
                    <?php foreach ($roles as $role): ?>
                    <tr>
                        <td class="fw-bold">
                            <i class="fas fa-user-shield text-info me-2"></i><?= e($role['name']) ?>
                        </td>
                        <td class="text-muted"><?= e($role['description'] ?? '—') ?></td>
                        <td class="text-center">
                            <span class="badge badge-primary"><?= number_format($role['permission_count'] ?? 0) ?></span>
                        </td>
                        <td>
                            <div class="btn-group-actions justify-content-end">
                                <a href="<?= url("roles/edit/{$role['id']}") ?>"
                                   class="btn btn-sm btn-outline" title="Edit">
                                    <i class="fas fa-edit"></i>Edit
                                </a>
                                <a href="<?= url("roles/permissions/{$role['id']}") ?>"
                                   class="btn btn-sm btn-outline" title="Manage Permissions">
                                    <i class="fas fa-key"></i>Permissions
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <i class="fas fa-user-shield"></i>
                                <h5>No roles found</h5>
                                <p>Create your first role to get started</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
