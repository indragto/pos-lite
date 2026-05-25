<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1">Categories</h5>
            <p class="text-muted mb-0">Manage product categories</p>
        </div>
        <a href="<?= url('categories/create') ?>" class="btn btn-primary" style="min-height: 44px;">
            <i class="fas fa-plus me-2"></i>Add Category
        </a>
    </div>

    <!-- Categories Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3" style="min-width: 200px;">Name</th>
                            <th class="d-none d-md-table-cell" style="min-width: 250px;">Description</th>
                            <th class="text-center">Products</th>
                            <th class="text-end pe-3" style="min-width: 160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box bg-primary bg-opacity-10 rounded-2 p-2 me-3">
                                            <i class="fas fa-tag text-primary"></i>
                                        </div>
                                        <div>
                                            <span class="fw-semibold"><?= e($category['name']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell text-muted">
                                    <?= e($category['description'] ?? '-') ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark fs-6 px-3 py-2">
                                        <?= (int)($category['product_count'] ?? 0) ?>
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group" role="group">
                                        <a href="<?= url("categories/edit/{$category['id']}") ?>"
                                           class="btn btn-sm btn-outline-primary"
                                           style="min-height: 44px; min-width: 44px;"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                            <span class="d-none d-lg-inline ms-1">Edit</span>
                                        </a>
                                        <form action="<?= url("categories/delete/{$category['id']}") ?>"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this category?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    style="min-height: 44px; min-width: 44px;"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                                <span class="d-none d-lg-inline ms-1">Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-tags fa-3x mb-3 d-block opacity-50"></i>
                                    <p class="mb-1">No categories found</p>
                                    <small>Create your first category to get started</small>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .icon-box {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .btn-group .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>
