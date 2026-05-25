<div class="page-header">
    <div><h1>Roles & Permissions</h1><p>Manage user roles</p></div>
    <div class="view-toggle">
        <button class="btn btn-outline view-toggle-btn active" data-view="list" data-container="viewContainer"><i class="fas fa-list"></i></button>
        <button class="btn btn-outline view-toggle-btn" data-view="grid" data-container="viewContainer"><i class="fas fa-th-large"></i></button>
    </div>
</div>

<div id="viewContainer">
    <div class="view-list"><div class="card"><div class="table-responsive">
        <table class="table"><thead><tr><th>Role</th><th>Description</th><th class="text-center">Permissions</th><th class="text-end">Actions</th></tr></thead>
        <tbody><?php if (!empty($roles)): foreach ($roles as $r): ?>
        <tr><td class="fw-bold"><?= e($r['name']) ?></td><td class="text-muted"><?= e($r['description'] ?? '—') ?></td><td class="text-center"><span class="badge badge-primary"><?= $r['permission_count'] ?? 0 ?></span></td>
        <td><div class="btn-group-actions justify-content-end"><a href="<?= url('roles/edit/'.$r['id']) ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></a><a href="<?= url('roles/permissions/'.$r['id']) ?>" class="btn btn-sm btn-primary"><i class="fas fa-shield-alt"></i>Permissions</a></div></td></tr>
        <?php endforeach; else: ?><tr><td colspan="4"><div class="empty-state"><i class="fas fa-user-shield"></i><h5>No roles found</h5></div></td></tr><?php endif; ?></tbody>
    </table></div></div></div>
    <div class="view-grid"><?php if (!empty($roles)): foreach ($roles as $r): ?>
    <div class="grid-card"><div style="width:44px;height:44px;border-radius:var(--radius);background:var(--primary-bg);display:flex;align-items:center;justify-content:center;margin-bottom:12px;"><i class="fas fa-user-shield text-primary fa-lg"></i></div><h5><?= e($r['name']) ?></h5><p><?= e($r['description'] ?? 'No description') ?></p>
        <div class="grid-meta"><span class="badge badge-primary"><?= $r['permission_count'] ?? 0 ?> permissions</span></div>
        <div class="grid-actions"><a href="<?= url('roles/edit/'.$r['id']) ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i>Edit</a><a href="<?= url('roles/permissions/'.$r['id']) ?>" class="btn btn-sm btn-primary"><i class="fas fa-shield-alt"></i>Permissions</a></div></div>
    <?php endforeach; else: ?><div class="empty-state" style="grid-column:1/-1;"><i class="fas fa-user-shield"></i><h5>No roles</h5></div><?php endif; ?></div>
</div>
