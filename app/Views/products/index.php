<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1">Products</h5>
            <p class="text-muted mb-0">Manage your product inventory</p>
        </div>
        <?php if (hasPermission('products.create')): ?>
        <a href="<?= url('products/create') ?>" class="btn btn-primary" style="min-height: 44px;">
            <i class="fas fa-plus me-2"></i>Add Product
        </a>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= url('products') ?>" class="row g-3">
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control" name="search" value="<?= e($search) ?>"
                               placeholder="Search by name or SKU...">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select class="form-select" name="category_id">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($categoryId ?? '') == $cat['id'] ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <button type="submit" class="btn btn-primary w-100" style="min-height: 44px;">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                </div>
                <div class="col-12 col-md-2">
                    <a href="<?= url('products') ?>" class="btn btn-outline-secondary w-100" style="min-height: 44px;">
                        <i class="fas fa-undo me-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">SKU</th>
                            <th>Name</th>
                            <th class="d-none d-md-table-cell">Category</th>
                            <th class="text-end">Price</th>
                            <th class="text-center">Stock</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $product): ?>
                            <?php
                                $stock = (int)($product['stock'] ?? 0);
                                $stockClass = $stock <= 0 ? 'danger' : ($stock <= 10 ? 'warning' : 'success');
                                $stockLabel = $stock <= 0 ? 'Out of Stock' : ($stock <= 10 ? 'Low Stock' : 'In Stock');
                            ?>
                            <tr>
                                <td class="ps-3">
                                    <span class="badge bg-light text-dark"><?= e($product['sku']) ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($product['image'])): ?>
                                        <img src="<?= url('uploads/' . $product['image']) ?>"
                                             alt="<?= e($product['name']) ?>"
                                             class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php endif; ?>
                                        <div>
                                            <span class="fw-semibold"><?= e($product['name']) ?></span>
                                            <?php if (!$product['is_active']): ?>
                                            <span class="badge bg-secondary ms-2">Inactive</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <?= e($product['category_name'] ?? '<span class="text-muted">Uncategorized</span>') ?>
                                </td>
                                <td class="text-end fw-bold"><?= formatRupiah($product['price']) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $stockClass ?> fs-6 px-3 py-2">
                                        <?= $stock ?>
                                        <span class="d-none d-lg-inline ms-1"><?= $stockLabel ?></span>
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group" role="group">
                                        <?php if (hasPermission('products.edit')): ?>
                                        <a href="<?= url("products/edit/{$product['id']}") ?>"
                                           class="btn btn-sm btn-outline-primary"
                                           style="min-height: 44px; min-width: 44px;" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if (hasPermission('products.delete')): ?>
                                        <form action="<?= url("products/delete/{$product['id']}") ?>"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete <?= e($product['name']) ?>?')">
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
                                    <i class="fas fa-box-open fa-3x mb-3 d-block opacity-50"></i>
                                    <p class="mb-1">No products found</p>
                                    <small>
                                        <?php if ($search || $categoryId): ?>
                                            Try adjusting your search or filters
                                            <a href="<?= url('products') ?>">Clear filters</a>
                                        <?php else: ?>
                                            Add your first product to get started
                                        <?php endif; ?>
                                    </small>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-transparent border-0 py-3">
            <nav aria-label="Products pagination">
                <ul class="pagination justify-content-center mb-0">
                    <?php
                    $queryParams = [];
                    if ($search) $queryParams['search'] = $search;
                    if ($categoryId) $queryParams['category_id'] = $categoryId;
                    $queryString = http_build_query($queryParams);
                    $baseUrl = url('products') . ($queryString ? '?' . $queryString . '&' : '?');
                    ?>
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= $baseUrl ?>page=<?= $page - 1 ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == 1 || $i == $totalPages || abs($i - $page) <= 2): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= $baseUrl ?>page=<?= $i ?>"><?= $i ?></a>
                        </li>
                        <?php elseif (abs($i - $page) == 3): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= $baseUrl ?>page=<?= $page + 1 ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>
