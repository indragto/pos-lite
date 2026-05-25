<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>Manage system users</p>
    </div>
    <?php if (hasPermission('users.create')): ?>
    <a href="<?= url('users/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i>Add User
    </a>
    <?php endif; ?>
</div>

<!-- Toolbar -->
<form method="GET" action="<?= url('users') ?>" class="toolbar mb-4">
    <input type="text" class="form-control" name="search" value="<?= e($search) ?>"
           placeholder="Search users...">
    <select class="form-select" name="role_id">
        <option value="">All Roles</option>
        <?php foreach ($roles as $role): ?>
        <option value="<?= $role['id'] ?>" <?= ($roleId ?? '') == $role['id'] ? 'selected' : '' ?>>
            <?= e($role['name']) ?>
        </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-filter"></i>Filter
    </button>
    <a href="<?= url('users') ?>" class="btn btn-outline">
        <i class="fas fa-undo"></i>Reset
    </a>
</form>

<!-- Users Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><code><?= e($user['username']) ?></code></td>
                        <td class="fw-bold"><?= e($user['full_name']) ?></td>
                        <td class="text-muted"><?= e($user['email'] ?? '—') ?></td>
                        <td><span class="badge badge-info"><?= e($user['role_name'] ?? '—') ?></span></td>
                        <td class="text-center">
                            <?php if ($user['is_active']): ?>
                            <span class="badge badge-success">Active</span>
                            <?php else: ?>
                            <span class="badge badge-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group-actions justify-content-end">
                                <?php if (hasPermission('users.edit')): ?>
                                <a href="<?= url("users/edit/{$user['id']}") ?>"
                                   class="btn btn-sm btn-outline" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (hasPermission('users.delete') && $user['id'] !== currentUser()['id']): ?>
                                <form action="<?= url("users/toggle/{$user['id']}") ?>" method="POST"
                                      onsubmit="return confirm('Toggle status for <?= e($user['full_name']) ?>?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline"
                                            title="<?= $user['is_active'] ? 'Deactivate' : 'Activate' ?>">
                                        <i class="fas fa-<?= $user['is_active'] ? 'ban' : 'check' ?>"></i>
                                    </button>
                                </form>
                                <form action="<?= url("users/delete/{$user['id']}") ?>" method="POST"
                                      onsubmit="return confirm('Delete <?= e($user['full_name']) ?>?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
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
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-users"></i>
                                <h5>No users found</h5>
                                <?php if (hasPermission('users.create')): ?>
                                <p>Add your first user to get started</p>
                                <a href="<?= url('users/create') ?>" class="btn btn-sm btn-primary mt-2">
                                    <i class="fas fa-plus"></i>Add User
                                </a>
                                <?php else: ?>
                                <p>Contact your administrator</p>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (($totalPages ?? 1) > 1): ?>
    <div class="card-footer">
        <nav>
            <ul class="pagination mb-0">
                <?php
                $uParams = [];
                if ($search ?? '') $uParams['search'] = $search;
                if ($roleId ?? '') $uParams['role_id'] = $roleId;
                $uQs = http_build_query($uParams);
                $uBase = url('users') . ($uQs ? '?' . $uQs . '&' : '?');
                ?>
                <?php if (($page ?? 1) > 1): ?>
                <li><a href="<?= $uBase ?>page=<?= ($page ?? 1) - 1 ?>"><i class="fas fa-chevron-left"></i></a></li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == 1 || $i == $totalPages || abs($i - ($page ?? 1)) <= 2): ?>
                    <li class="<?= $i == ($page ?? 1) ? 'active' : '' ?>">
                        <a href="<?= $uBase ?>page=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php elseif (abs($i - ($page ?? 1)) == 3): ?>
                    <li><span>...</span></li>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if (($page ?? 1) < $totalPages): ?>
                <li><a href="<?= $uBase ?>page=<?= ($page ?? 1) + 1 ?>"><i class="fas fa-chevron-right"></i></a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>
