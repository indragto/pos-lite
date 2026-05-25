<div class="page-header">
    <div><h1>Users</h1><p>Manage system users</p></div>
    <div class="d-flex gap-2 align-items-center">
        <div class="view-toggle">
            <button class="btn btn-outline view-toggle-btn active" data-view="list" data-container="viewContainer"><i class="fas fa-list"></i></button>
            <button class="btn btn-outline view-toggle-btn" data-view="grid" data-container="viewContainer"><i class="fas fa-th-large"></i></button>
        </div>
        <a href="<?= url('users/create') ?>" class="btn btn-primary"><i class="fas fa-plus"></i>Add User</a>
    </div>
</div>

<form method="GET" action="<?= url('users') ?>" class="toolbar mb-4">
    <input type="text" class="form-control" name="search" value="<?= e($search ?? '') ?>" placeholder="Search..." style="min-width:200px;">
    <select class="form-select" name="role_id"><option value="">All Roles</option><?php foreach ($roles ?? [] as $r): ?><option value="<?= $r['id'] ?>" <?= ($roleId ?? '') == $r['id'] ? 'selected' : '' ?>><?= e($r['name']) ?></option><?php endforeach; ?></select>
    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i>Filter</button>
</form>

<div id="viewContainer">
    <div class="view-list"><div class="card"><div class="table-responsive">
        <table class="table"><thead><tr><th>Username</th><th>Full Name</th><th>Email</th><th>Role</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
        <tbody><?php if (!empty($users)): foreach ($users as $u): ?>
        <tr><td class="fw-bold"><?= e($u['username']) ?></td><td><?= e($u['full_name']) ?></td><td class="text-muted"><?= e($u['email'] ?? '—') ?></td>
        <td><span class="badge badge-primary"><?= e($u['role_name'] ?? '—') ?></span></td>
        <td><span class="badge <?= $u['is_active'] ? 'badge-success' : 'badge-danger' ?>"><?= $u['is_active'] ? 'Active' : 'Inactive' ?></span></td>
        <td><div class="btn-group-actions justify-content-end"><a href="<?= url('users/edit/'.$u['id']) ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></a><form action="<?= url('users/toggle/'.$u['id']) ?>" method="POST" class="d-inline"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-ghost <?= $u['is_active'] ? 'text-warning' : 'text-success' ?>"><i class="fas fa-<?= $u['is_active'] ? 'ban' : 'check' ?>"></i></button></form><form action="<?= url('users/delete/'.$u['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete user?')"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form></div></td></tr>
        <?php endforeach; else: ?><tr><td colspan="6"><div class="empty-state"><i class="fas fa-users"></i><h5>No users found</h5></div></td></tr><?php endif; ?></tbody>
    </table></div></div></div>
    <div class="view-grid"><?php if (!empty($users)): foreach ($users as $u): ?>
    <div class="grid-card"><div class="d-flex align-items-center gap-3 mb-3"><div style="width:44px;height:44px;border-radius:var(--radius);background:var(--primary-bg);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--primary);"><?= strtoupper(substr($u['full_name'],0,1)) ?></div><div><h5 class="mb-0"><?= e($u['full_name']) ?></h5><p>@<?= e($u['username']) ?></p></div></div>
        <div class="grid-meta"><span class="badge badge-primary"><?= e($u['role_name'] ?? '—') ?></span><span class="badge <?= $u['is_active'] ? 'badge-success' : 'badge-danger' ?>"><?= $u['is_active'] ? 'Active' : 'Inactive' ?></span></div>
        <div class="grid-actions"><a href="<?= url('users/edit/'.$u['id']) ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i>Edit</a><form action="<?= url('users/delete/'.$u['id']) ?>" method="POST" onsubmit="return confirm('Delete?')"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form></div></div>
    <?php endforeach; else: ?><div class="empty-state" style="grid-column:1/-1;"><i class="fas fa-users"></i><h5>No users</h5></div><?php endif; ?></div>
</div>

<?php if ($totalPages > 1): ?><div class="card mt-3"><div class="card-footer"><nav><ul class="pagination mb-0"><?php $qs = http_build_query(['search' => $search ?? '', 'role_id' => $roleId ?? '']); $bu = url('users') . ($qs ? '?'.$qs.'&' : '?'); ?><?php if ($page > 1): ?><li><a href="<?= $bu ?>page=<?= $page-1 ?>"><i class="fas fa-chevron-left"></i></a></li><?php endif; ?><?php for ($i = 1; $i <= $totalPages; $i++): if ($i==1||$i==$totalPages||abs($i-$page)<=2): ?><li class="<?= $i==$page?'active':'' ?>"><a href="<?= $bu ?>page=<?= $i ?>"><?= $i ?></a></li><?php endif; endfor; ?><?php if ($page < $totalPages): ?><li><a href="<?= $bu ?>page=<?= $page+1 ?>"><i class="fas fa-chevron-right"></i></a></li><?php endif; ?></ul></nav></div></div><?php endif; ?>
