<div class="page-header">
    <div>
        <h1>Products</h1>
        <p>Manage your product inventory</p>
    </div>
    <?php if (hasPermission('products.create')): ?>
    <a href="<?= url('products/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i>Add Product
    </a>
    <?php endif; ?>
</div>

<!-- Toolbar -->
<form method="GET" action="<?= url('products') ?>" class="toolbar mb-4">
    <input type="text" class="form-control" name="search" value="<?= e($search) ?>"
           placeholder="Search by name or SKU..." style="min-width: 220px;">
    <select class="form-select" name="category_id">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?>
        <option value="<?= $cat['id'] ?>" <?= ($categoryId ?? '') == $cat['id'] ? 'selected' : '' ?>>
            <?= e($cat['name']) ?>
        </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-filter"></i>Filter
    </button>
    <a href="<?= url('products') ?>" class="btn btn-outline">
        <i class="fas fa-undo"></i>Reset
    </a>
</form>

<!-- Products Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>SKU</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th class="text-end">Price</th>
                    <th class="text-center">Stock</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                    <?php
                        $stock = (int)($product['stock'] ?? 0);
                        if ($stock > 10) {
                            $stockBadge = 'badge-success';
                            $stockText = 'In Stock';
                        } elseif ($stock > 0) {
                            $stockBadge = 'badge-warning';
                            $stockText = 'Low Stock';
                        } else {
                            $stockBadge = 'badge-danger';
                            $stockText = 'Out of Stock';
                        }
                    ?>
                    <tr>
                        <td>
                            <?php if (!empty($product['image'])): ?>
                            <img src="<?= url('uploads/' . $product['image']) ?>"
                                 alt="<?= e($product['name']) ?>"
                                 class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                            <?php else: ?>
                            <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                 style="width: 40px; height: 40px;">
                                <i class="fas fa-image text-muted" style="font-size: 14px;"></i>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge bg-light text-dark"><?= e($product['sku']) ?></span></td>
                        <td class="fw-bold"><?= e($product['name']) ?></td>
                        <td><?= e($product['category_name'] ?? '—') ?></td>
                        <td class="text-end fw-bold"><?= formatRupiah($product['price']) ?></td>
                        <td class="text-center">
                            <span class="badge <?= $stockBadge ?>"><?= $stock ?> <?= $stockText ?></span>
                        </td>
                        <td>
                            <?php if ($product['is_active']): ?>
                            <span class="badge badge-success">Active</span>
                            <?php else: ?>
                            <span class="badge" style="background: var(--gray-200); color: var(--gray-600);">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group-actions justify-content-end">
                                <?php if (hasPermission('products.edit')): ?>
                                <a href="<?= url("products/edit/{$product['id']}") ?>"
                                   class="btn btn-sm btn-outline" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (hasPermission('products.delete')): ?>
                                <form action="<?= url("products/delete/{$product['id']}") ?>"
                                      method="POST"
                                      onsubmit="return confirm('Delete <?= e($product['name']) ?>?')">
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
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="fas fa-box-open"></i>
                                <h5>No products found</h5>
                                <?php if ($search || $categoryId): ?>
                                <p>Try adjusting your search or filters</p>
                                <a href="<?= url('products') ?>" class="btn btn-sm btn-outline mt-2">Clear filters</a>
                                <?php else: ?>
                                <p>Add your first product to get started</p>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="card-footer">
        <nav>
            <ul class="pagination mb-0">
                <?php
                $queryParams = [];
                if ($search) $queryParams['search'] = $search;
                if ($categoryId) $queryParams['category_id'] = $categoryId;
                $queryString = http_build_query($queryParams);
                $baseUrl = url('products') . ($queryString ? '?' . $queryString . '&' : '?');
                ?>
                <?php if ($page > 1): ?>
                <li>
                    <a href="<?= $baseUrl ?>page=<?= $page - 1 ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == 1 || $i == $totalPages || abs($i - $page) <= 2): ?>
                    <li class="<?= $i == $page ? 'active' : '' ?>">
                        <a href="<?= $baseUrl ?>page=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php elseif (abs($i - $page) == 3): ?>
                    <li><span>...</span></li>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                <li>
                    <a href="<?= $baseUrl ?>page=<?= $page + 1 ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>
