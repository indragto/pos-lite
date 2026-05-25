<div class="page-header">
    <div><h1>Categories</h1><p>Organize your products</p></div>
    <div class="d-flex gap-2 align-items-center">
        <div class="view-toggle">
            <button class="btn btn-outline view-toggle-btn active" data-view="list" data-container="viewContainer"><i class="fas fa-list"></i></button>
            <button class="btn btn-outline view-toggle-btn" data-view="grid" data-container="viewContainer"><i class="fas fa-th-large"></i></button>
        </div>
        <a href="<?= url('categories/create') ?>" class="btn btn-primary"><i class="fas fa-plus"></i>Add Category</a>
    </div>
</div>

<div id="viewContainer">
    <div class="view-list">
        <div class="card"><div class="table-responsive">
            <table class="table"><thead><tr><th>Name</th><th>Description</th><th class="text-center">Products</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                <?php if (!empty($categories)): foreach ($categories as $cat): ?>
                <tr><td class="fw-bold"><?= e($cat['name']) ?></td><td class="text-muted"><?= e($cat['description'] ?? '—') ?></td><td class="text-center"><span class="badge badge-primary"><?= $cat['product_count'] ?? 0 ?></span></td>
                <td><div class="btn-group-actions justify-content-end"><a href="<?= url('categories/edit/' . $cat['id']) ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></a><form action="<?= url('categories/delete/' . $cat['id']) ?>" method="POST" onsubmit="return confirm('Delete this category?')"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form></div></td></tr>
                <?php endforeach; else: ?>
                <tr><td colspan="4"><div class="empty-state"><i class="fas fa-tags"></i><h5>No categories</h5></div></td></tr>
                <?php endif; ?>
            </tbody></table>
        </div></div>
    </div>
    <div class="view-grid">
        <?php if (!empty($categories)): foreach ($categories as $cat): ?>
        <div class="grid-card"><h5><?= e($cat['name']) ?></h5><p><?= e($cat['description'] ?? 'No description') ?></p>
            <div class="grid-meta"><span class="badge badge-primary"><?= $cat['product_count'] ?? 0 ?> products</span></div>
            <div class="grid-actions"><a href="<?= url('categories/edit/' . $cat['id']) ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i>Edit</a><form action="<?= url('categories/delete/' . $cat['id']) ?>" method="POST" onsubmit="return confirm('Delete?')"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form></div>
        </div>
        <?php endforeach; else: ?>
        <div class="empty-state" style="grid-column:1/-1;"><i class="fas fa-tags"></i><h5>No categories</h5></div>
        <?php endif; ?>
    </div>
</div>
