<div class="page-header">
    <div>
        <h1>Products</h1>
        <p><?= count($products ?? []) ?> products in inventory</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <div class="view-toggle">
            <button class="btn btn-outline view-toggle-btn active" data-view="list" data-container="viewContainer" title="List View"><i class="fas fa-list"></i></button>
            <button class="btn btn-outline view-toggle-btn" data-view="grid" data-container="viewContainer" title="Grid View"><i class="fas fa-th-large"></i></button>
        </div>
        <?php if (hasPermission('products.create')): ?>
        <a href="<?= url('products/create') ?>" class="btn btn-primary"><i class="fas fa-plus"></i>Add Product</a>
        <?php endif; ?>
    </div>
</div>

<form method="GET" action="<?= url('products') ?>" class="toolbar mb-4">
    <input type="text" class="form-control" name="search" value="<?= e($search) ?>" placeholder="Search name or SKU..." style="min-width:220px;">
    <select class="form-select" name="category_id">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?>
        <option value="<?= $cat['id'] ?>" <?= ($categoryId ?? '') == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i>Filter</button>
    <a href="<?= url('products') ?>" class="btn btn-outline"><i class="fas fa-undo"></i>Reset</a>
</form>

<div id="viewContainer">
    <!-- List View -->
    <div class="view-list">
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Image</th><th>SKU</th><th>Name</th><th>Category</th><th class="text-end">Price</th><th class="text-center">Stock</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                        <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                        <?php $stock = (int)($product['stock'] ?? 0); $stockBadge = $stock > 10 ? 'badge-success' : ($stock > 0 ? 'badge-warning' : 'badge-danger'); $stockText = $stock > 10 ? 'In Stock' : ($stock > 0 ? 'Low Stock' : 'Out of Stock'); ?>
                        <tr>
                            <td>
                                <?php if (!empty($product['image'])): ?><img src="<?= url('uploads/' . $product['image']) ?>" class="rounded" style="width:40px;height:40px;object-fit:cover;"><?php else: ?><div class="rounded bg-light d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="fas fa-image text-muted" style="font-size:14px;"></i></div><?php endif; ?>
                            </td>
                            <td><span class="badge bg-light text-dark"><?= e($product['sku']) ?></span></td>
                            <td class="fw-bold"><?= e($product['name']) ?></td>
                            <td><?= e($product['category_name'] ?? '—') ?></td>
                            <td class="text-end fw-bold"><?= formatRupiah($product['price']) ?></td>
                            <td class="text-center"><span class="badge <?= $stockBadge ?>"><?= $stock ?> <?= $stockText ?></span></td>
                            <td><?php if ($product['is_active']): ?><span class="badge badge-success">Active</span><?php else: ?><span class="badge" style="background:var(--gray-200);color:var(--gray-600);">Inactive</span><?php endif; ?></td>
                            <td><div class="btn-group-actions justify-content-end">
                                <?php if (hasPermission('products.edit')): ?><a href="<?= url("products/edit/{$product['id']}") ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></a><?php endif; ?>
                                <?php if (hasPermission('products.delete')): ?><form action="<?= url("products/delete/{$product['id']}") ?>" method="POST" onsubmit="return confirm('Delete <?= e($product['name']) ?>?')"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form><?php endif; ?>
                            </div></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr><td colspan="8"><div class="empty-state"><i class="fas fa-box-open"></i><h5>No products found</h5><?php if ($search || $categoryId): ?><p>Try adjusting filters</p><a href="<?= url('products') ?>" class="btn btn-sm btn-outline mt-2">Clear filters</a><?php else: ?><p>Add your first product</p><?php endif; ?></div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Grid View -->
    <div class="view-grid">
        <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
        <?php $stock = (int)($product['stock'] ?? 0); $stockBadge = $stock > 10 ? 'badge-success' : ($stock > 0 ? 'badge-warning' : 'badge-danger'); ?>
        <div class="grid-card">
            <div class="d-flex align-items-start gap-3">
                <?php if (!empty($product['image'])): ?><img src="<?= url('uploads/' . $product['image']) ?>" class="rounded" style="width:56px;height:56px;object-fit:cover;flex-shrink:0;"><?php else: ?><div class="rounded bg-light d-flex align-items-center justify-content-center" style="width:56px;height:56px;flex-shrink:0;"><i class="fas fa-box text-muted fa-lg"></i></div><?php endif; ?>
                <div class="flex-grow-1">
                    <h5><?= e($product['name']) ?></h5>
                    <p><span class="badge bg-light text-dark"><?= e($product['sku']) ?></span> <?= e($product['category_name'] ?? '') ?></p>
                </div>
            </div>
            <div class="grid-meta">
                <span class="badge badge-primary"><?= formatRupiah($product['price']) ?></span>
                <span class="badge <?= $stockBadge ?>"><?= $stock ?> in stock</span>
                <?php if ($product['is_active']): ?><span class="badge badge-success">Active</span><?php else: ?><span class="badge badge-danger">Inactive</span><?php endif; ?>
            </div>
            <div class="grid-actions">
                <?php if (hasPermission('products.edit')): ?><a href="<?= url("products/edit/{$product['id']}") ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i>Edit</a><?php endif; ?>
                <?php if (hasPermission('products.delete')): ?><form action="<?= url("products/delete/{$product['id']}") ?>" method="POST" onsubmit="return confirm('Delete <?= e($product['name']) ?>?')"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form><?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="empty-state" style="grid-column:1/-1;"><i class="fas fa-box-open"></i><h5>No products found</h5></div>
        <?php endif; ?>
    </div>
</div>

<?php if ($totalPages > 1): ?>
<div class="card mt-3">
    <div class="card-footer">
        <nav><ul class="pagination mb-0">
            <?php $qp = []; if ($search) $qp['search'] = $search; if ($categoryId) $qp['category_id'] = $categoryId; $qs = http_build_query($qp); $bu = url('products') . ($qs ? '?' . $qs . '&' : '?'); ?>
            <?php if ($page > 1): ?><li><a href="<?= $bu ?>page=<?= $page - 1 ?>"><i class="fas fa-chevron-left"></i></a></li><?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): if ($i == 1 || $i == $totalPages || abs($i - $page) <= 2): ?><li class="<?= $i == $page ? 'active' : '' ?>"><a href="<?= $bu ?>page=<?= $i ?>"><?= $i ?></a></li><?php elseif (abs($i - $page) == 3): ?><li><span>...</span></li><?php endif; endfor; ?>
            <?php if ($page < $totalPages): ?><li><a href="<?= $bu ?>page=<?= $page + 1 ?>"><i class="fas fa-chevron-right"></i></a></li><?php endif; ?>
        </ul></nav>
    </div>
</div>
<?php endif; ?>
