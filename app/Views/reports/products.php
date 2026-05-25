  <?php
/**
 * Product Sales Report View
 * Variables: $title, $products, $start_date, $end_date, $total_revenue, $total_items_sold
 */
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1"><i class="fas fa-box-open me-2"></i>Product Sales Report</h5>
            <p class="text-muted mb-0"><?= formatDate($start_date) ?> - <?= formatDate($end_date) ?></p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="window.print()" style="min-height: 44px;">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= url('reports/products') ?>" class="row g-3">
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted">From Date</label>
                    <input type="date" class="form-control" name="start_date" value="<?= e($start_date) ?>">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted">To Date</label>
                    <input type="date" class="form-control" name="end_date" value="<?= e($end_date) ?>">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted">Category</label>
                    <select class="form-select" name="category_id">
                        <option value="">All Categories</option>
                        <?php foreach ($categories ?? [] as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($categoryId ?? '') == $cat['id'] ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1" style="min-height: 44px;">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="<?= url('reports/products') ?>" class="btn btn-outline-secondary" style="min-height: 44px;">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 56px; height: 56px;">
                        <i class="fas fa-money-bill-wave fa-lg text-primary"></i>
                    </div>
                    <h6 class="text-muted mb-1">Total Revenue</h6>
                    <h4 class="fw-bold mb-0"><?= formatRupiah($total_revenue ?? 0) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 56px; height: 56px;">
                        <i class="fas fa-box fa-lg text-success"></i>
                    </div>
                    <h6 class="text-muted mb-1">Items Sold</h6>
                    <h4 class="fw-bold mb-0"><?= number_format($total_items_sold ?? 0) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 56px; height: 56px;">
                        <i class="fas fa-list fa-lg text-info"></i>
                    </div>
                    <h6 class="text-muted mb-1">Products Sold</h6>
                    <h4 class="fw-bold mb-0"><?= number_format(count($products ?? [])) ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3" style="width: 40px;">#</th>
                            <th>Product</th>
                            <th class="d-none d-md-table-cell">Category</th>
                            <th class="text-center">Qty Sold</th>
                            <th class="text-end">Revenue</th>
                            <th class="text-end pe-3">Avg. Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($products)): ?>
                            <?php $rank = 1; foreach ($products as $product): ?>
                            <tr>
                                <td class="ps-3">
                                    <span class="badge bg-light text-dark"><?= $rank ?></span>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-semibold"><?= e($product['name']) ?></span>
                                        <br><small class="text-muted"><code><?= e($product['sku']) ?></code></small>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="badge bg-secondary"><?= e($product['category_name'] ?? '-') ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary fs-6 px-3 py-2"><?= number_format($product['total_qty']) ?></span>
                                </td>
                                <td class="text-end fw-bold"><?= formatRupiah($product['total_revenue']) ?></td>
                                <td class="text-end pe-3"><?= formatRupiah($product['avg_price']) ?></td>
                            </tr>
                            <?php $rank++; endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-chart-bar fa-3x mb-3 d-block opacity-50"></i>
                                    <p class="mb-1">No sales data for this period</p>
                                    <small>Try adjusting your date range or filters</small>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>