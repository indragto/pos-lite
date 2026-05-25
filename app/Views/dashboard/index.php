<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="icon-box bg-primary bg-opacity-10 rounded-3 p-2 me-3">
                            <i class="fas fa-coins text-primary fa-lg"></i>
                        </div>
                    </div>
                    <h6 class="text-muted mb-1">Today's Sales</h6>
                    <h4 class="mb-0 fw-bold"><?= formatRupiah($todaySummary['total'] ?? 0) ?></h4>
                    <small class="text-success">
                        <i class="fas fa-arrow-up me-1"></i><?= (int)($todaySummary['transaction_count'] ?? 0) ?> transactions
                    </small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="icon-box bg-success bg-opacity-10 rounded-3 p-2 me-3">
                            <i class="fas fa-receipt text-success fa-lg"></i>
                        </div>
                    </div>
                    <h6 class="text-muted mb-1">Transactions</h6>
                    <h4 class="mb-0 fw-bold"><?= (int)($todaySummary['transaction_count'] ?? 0) ?></h4>
                    <small class="text-muted">Today</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="icon-box bg-info bg-opacity-10 rounded-3 p-2 me-3">
                            <i class="fas fa-box-open text-info fa-lg"></i>
                        </div>
                    </div>
                    <h6 class="text-muted mb-1">Products Sold</h6>
                    <h4 class="mb-0 fw-bold"><?= (int)($todaySummary['total_items'] ?? 0) ?></h4>
                    <small class="text-muted">Items today</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="icon-box bg-warning bg-opacity-10 rounded-3 p-2 me-3">
                            <i class="fas fa-chart-line text-warning fa-lg"></i>
                        </div>
                    </div>
                    <h6 class="text-muted mb-1">Week Sales</h6>
                    <h4 class="mb-0 fw-bold"><?= formatRupiah($weekSummary['total'] ?? 0) ?></h4>
                    <small class="text-muted">This week</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Low Stock Alert Widget -->
        <?php if (!empty($lowStockProducts)): ?>
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>Low Stock Alert
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach ($lowStockProducts as $product): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <div class="fw-semibold"><?= e($product['name']) ?></div>
                                <small class="text-muted">
                                    <span class="badge bg-light text-dark"><?= e($product['sku']) ?></span>
                                </small>
                            </div>
                            <span class="badge <?= ($product['stock'] ?? 0) <= 0 ? 'bg-danger' : 'bg-warning text-dark' ?> fs-6 px-3 py-2">
                                <?= (int)($product['stock'] ?? 0) ?> left
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 py-3">
                    <a href="<?= url('products') ?>" class="btn btn-sm btn-outline-primary w-100">
                        <i class="fas fa-box me-1"></i>View All Products
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recent Transactions -->
        <div class="col-12 <?= empty($lowStockProducts) ? 'col-xl-8' : 'col-xl-4' ?>">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-clock-rotate-left text-primary me-2"></i>Recent Transactions
                    </h6>
                    <a href="<?= url('transactions') ?>" class="btn btn-sm btn-outline-primary">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Invoice</th>
                                    <th>Total</th>
                                    <th class="d-none d-md-table-cell">Payment</th>
                                    <th class="d-none d-lg-table-cell">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentTransactions)): ?>
                                    <?php foreach ($recentTransactions as $txn): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <a href="<?= url("transactions/{$txn['id']}") ?>" class="text-decoration-none fw-semibold">
                                                <?= e($txn['invoice_no']) ?>
                                            </a>
                                        </td>
                                        <td class="fw-bold"><?= formatRupiah($txn['total']) ?></td>
                                        <td class="d-none d-md-table-cell">
                                            <span class="badge
                                                <?= $txn['payment_method'] === 'cash' ? 'bg-success' : '' ?>
                                                <?= $txn['payment_method'] === 'card' ? 'bg-primary' : '' ?>
                                                <?= $txn['payment_method'] === 'qris' ? 'bg-info' : '' ?>
                                            ">
                                                <i class="fas fa-<?= $txn['payment_method'] === 'cash' ? 'money-bill' : '' ?><?= $txn['payment_method'] === 'card' ? 'credit-card' : '' ?><?= $txn['payment_method'] === 'qris' ? 'qrcode' : '' ?> me-1"></i>
                                                <?= ucfirst($txn['payment_method']) ?>
                                            </span>
                                        </td>
                                        <td class="d-none d-lg-table-cell text-muted">
                                            <?= formatDate($txn['created_at'], 'H:i') ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            No transactions today
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="col-12 <?= empty($lowStockProducts) ? 'col-xl-4' : 'col-xl-4' ?>">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-fire text-danger me-2"></i>Top Products Today
                    </h6>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($topProducts)): ?>
                        <?php $rank = 1; foreach ($topProducts as $product): ?>
                        <div class="d-flex align-items-center px-3 py-3 <?= $rank > 1 ? 'border-top' : '' ?>">
                            <div class="me-3">
                                <span class="badge bg-light text-dark rounded-circle d-flex align-items-center justify-content-center"
                                      style="width: 36px; height: 36px; font-size: 0.85rem;">
                                    <?= $rank ?>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold"><?= e($product['product_name']) ?></div>
                                <small class="text-muted"><?= e($product['sku']) ?></small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold"><?= (int)$product['total_qty'] ?> sold</div>
                                <small class="text-success"><?= formatRupiah($product['total_revenue']) ?></small>
                            </div>
                        </div>
                        <?php $rank++; endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-bar fa-2x mb-2 d-block"></i>
                            No sales data yet
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mt-2">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="row g-2 text-center">
                        <?php if (hasPermission('transactions.pos')): ?>
                        <div class="col-6 col-md-3">
                            <a href="<?= url('pos') ?>" class="btn btn-success w-100 py-3" style="min-height: 56px;">
                                <i class="fas fa-shopping-cart d-block mb-1 fa-lg"></i>
                                <span>New Sale</span>
                            </a>
                        </div>
                        <?php endif; ?>
                        <?php if (hasPermission('products.create')): ?>
                        <div class="col-6 col-md-3">
                            <a href="<?= url('products/create') ?>" class="btn btn-outline-primary w-100 py-3" style="min-height: 56px;">
                                <i class="fas fa-plus d-block mb-1 fa-lg"></i>
                                <span>Add Product</span>
                            </a>
                        </div>
                        <?php endif; ?>
                        <?php if (hasPermission('reports.view')): ?>
                        <div class="col-6 col-md-3">
                            <a href="<?= url('reports/daily') ?>" class="btn btn-outline-info w-100 py-3" style="min-height: 56px;">
                                <i class="fas fa-chart-bar d-block mb-1 fa-lg"></i>
                                <span>Reports</span>
                            </a>
                        </div>
                        <?php endif; ?>
                        <?php if (hasPermission('transactions.view')): ?>
                        <div class="col-6 col-md-3">
                            <a href="<?= url('transactions') ?>" class="btn btn-outline-secondary w-100 py-3" style="min-height: 56px;">
                                <i class="fas fa-history d-block mb-1 fa-lg"></i>
                                <span>History</span>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .icon-box {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card {
        transition: transform 0.15s ease;
    }
    .card:hover {
        transform: translateY(-2px);
    }
</style>
