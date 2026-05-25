  <?php
/**
 * Users Index View
 * Variables: $title, $users, $roles
 */
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1"><i class="fas fa-users me-2"></i>Users</h5>
            <p class="text-muted mb-0">Manage system users and their access</p>
        </div>
        <?php if (hasPermission('users.create')): ?>
        <a href="<?= url('users/create') ?>" class="btn btn-primary" style="min-height: 44px;">
            <i class="fas fa-plus me-2"></i>Add User
        </a>
        <?php endif; ?>
    </div>

    <!-- Users List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">User</th>
                            <th class="d-none d-md-table-cell">Username</th>
                            <th class="d-none d-lg-table-cell">Role</th>
                            <th class="text-center">Status</th>
                            <th class="d-none d-lg-table-cell">Last Login</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3
                                                    <?= $user['is_active'] ? 'bg-primary' : 'bg-secondary' ?>"
                                             style="width: 44px; height: 44px;">
                                            <span class="text-white fw-bold fs-5">
                                                <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                                            </span>
                                        </div>
                                        <div>
                                            <span class="fw-semibold"><?= e($user['full_name']) ?></span>
                                            <br><small class="text-muted"><?= e($user['email'] ?? '-') ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <code><?= e($user['username']) ?></code>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <span class="badge bg-info"><?= e($user['role_name'] ?? 'No Role') ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($user['is_active']): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success">
                                        <i class="fas fa-check-circle me-1"></i>Active
                                    </span>
                                    <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger">
                                        <i class="fas fa-times-circle me-1"></i>Inactive
                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td class="d-none d-lg-table-cell text-muted">
                                    <?= $user['last_login'] ? formatDate($user['last_login']) : '<span class="text-muted">Never</span>' ?>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group" role="group">
                                        <?php if (hasPermission('users.edit')): ?>
                                        <a href="<?= url("users/edit/{$user['id']}") ?>"
                                           class="btn btn-sm btn-outline-primary"
                                           style="min-height: 44px; min-width: 44px;" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if (hasPermission('users.delete') && $user['id'] !== currentUser()['id']): ?>
                                        <form action="<?= url("users/delete/{$user['id']}") ?>"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete <?= e($user['full_name']) ?>?')">
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
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-users fa-3x mb-3 d-block opacity-50"></i>
                                    <p class="mb-1">No users found</p>
                                    <small>
                                        <?php if (hasPermission('users.create')): ?>
                                            Add your first user to get started
                                        <?php else: ?>
                                            Contact your administrator to add users
                                        <?php endif; ?>
                                    </small>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>