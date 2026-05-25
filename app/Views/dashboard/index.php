<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>Overview of your store performance</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('pos') ?>" class="btn btn-primary">
            <i class="fas fa-shopping-cart"></i>New Sale
        </a>
    </div>
</div>

<!-- Period Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Today's Sales</div>
                <div class="stat-value"><?= formatRupiah($todaySummary['total_sales'] ?? 0) ?></div>
                <div class="stat-change up">
                    <i class="fas fa-receipt"></i><?= (int)($todaySummary['total_transactions'] ?? 0) ?> transactions
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">This Week</div>
                <div class="stat-value"><?= formatRupiah($weekSummary['total_sales'] ?? 0) ?></div>
                <div class="stat-change up">
                    <i class="fas fa-receipt"></i><?= (int)($weekSummary['total_transactions'] ?? 0) ?> transactions
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon info">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">This Month</div>
                <div class="stat-value"><?= formatRupiah($monthSummary['total_sales'] ?? 0) ?></div>
                <div class="stat-change up">
                    <i class="fas fa-receipt"></i><?= (int)($monthSummary['total_transactions'] ?? 0) ?> transactions
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Breakdown Row -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Subtotal</div>
                <div class="stat-value"><?= formatRupiah($todaySummary['total_subtotal'] ?? 0) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-percent"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Tax Collected</div>
                <div class="stat-value"><?= formatRupiah($todaySummary['total_tax'] ?? 0) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-tag"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Discounts Given</div>
                <div class="stat-value"><?= formatRupiah($todaySummary['total_discount'] ?? 0) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Avg Transaction</div>
                <div class="stat-value"><?= formatRupiah($todaySummary['avg_transaction'] ?? 0) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Low Stock Products -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h6><i class="fas fa-exclamation-triangle text-warning me-2"></i>Low Stock Alert</h6>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($lowStockProducts)): ?>
                    <div class="table-responsive" style="border: none;">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lowStockProducts as $product): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?= e($product['name']) ?></div>
                                        <?php if (!empty($product['category_name'])): ?>
                                            <small class="text-muted"><?= e($product['category_name']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php $stock = (int)($product['stock'] ?? 0); ?>
                                        <?php if ($stock <= 0): ?>
                                            <span class="badge badge-danger">Out of stock</span>
                                        <?php elseif ($stock <= 5): ?>
                                            <span class="badge badge-warning"><?= $stock ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-primary"><?= $stock ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <h5>All Stocked Up</h5>
                        <p>No products are running low on inventory.</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <a href="<?= url('products') ?>" class="btn btn-outline btn-sm btn-block">
                    <i class="fas fa-box"></i>View All Products
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <h6><i class="fas fa-clock text-primary me-2"></i>Recent Transactions</h6>
                <a href="<?= url('transactions') ?>" class="btn btn-outline btn-sm">
                    View All <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($recentTransactions)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Date</th>
                                    <th>Cashier</th>
                                    <th>Payment</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentTransactions as $txn): ?>
                                <tr>
                                    <td>
                                        <a href="<?= url("transactions/" . ($txn['id'] ?? '')) ?>" class="text-primary fw-bold text-decoration-none">
                                            <?= e($txn['invoice_no']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="text-muted"><?= formatDate($txn['created_at'], 'd/m/Y H:i') ?></span>
                                    </td>
                                    <td><?= e($txn['cashier_name'] ?? '-') ?></td>
                                    <td>
                                        <?php
                                        $method = $txn['payment_method'] ?? '';
                                        $badgeClass = match ($method) {
                                            'cash' => 'badge-success',
                                            'card' => 'badge-primary',
                                            'qris' => 'badge-info',
                                            default => 'badge-warning',
                                        };
                                        $icon = match ($method) {
                                            'cash' => 'fa-money-bill',
                                            'card' => 'fa-credit-card',
                                            'qris' => 'fa-qrcode',
                                            default => 'fa-wallet',
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <i class="fas <?= $icon ?> me-1"></i><?= ucfirst($method) ?>
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold"><?= formatRupiah($txn['total'] ?? 0) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h5>No Transactions Yet</h5>
                        <p>Start a new sale to see transactions appear here.</p>
                        <a href="<?= url('pos') ?>" class="btn btn-primary btn-sm mt-3">
                            <i class="fas fa-shopping-cart"></i>Open POS
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Top Products -->
<div class="row g-3 mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-fire text-danger me-2"></i>Top Products Today</h6>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($topProducts)): ?>
                    <div class="table-responsive" style="border: none;">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Product</th>
                                    <th class="text-center">Qty Sold</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $rank = 1; foreach ($topProducts as $product): ?>
                                <tr>
                                    <td>
                                        <?php
                                        $rankBadge = match ($rank) {
                                            1 => 'badge-warning',
                                            2 => 'badge-primary',
                                            3 => 'badge-info',
                                            default => 'badge-secondary',
                                        };
                                        ?>
                                        <span class="badge <?= $rankBadge ?>"><?= $rank ?></span>
                                    </td>
                                    <td class="fw-bold"><?= e($product['product_name']) ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-success"><?= (int)$product['total_qty'] ?></span>
                                    </td>
                                    <td class="text-end fw-bold text-success"><?= formatRupiah($product['total_revenue'] ?? 0) ?></td>
                                </tr>
                                <?php $rank++; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-chart-bar"></i>
                        <h5>No Sales Data Yet</h5>
                        <p>Complete your first sale to see product performance.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-bolt text-warning me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <?php if (hasPermission('transactions.pos')): ?>
                    <div class="col-6 col-md-3">
                        <a href="<?= url('pos') ?>" class="btn btn-success btn-block">
                            <i class="fas fa-shopping-cart"></i>New Sale
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php if (hasPermission('products.create')): ?>
                    <div class="col-6 col-md-3">
                        <a href="<?= url('products/create') ?>" class="btn btn-outline btn-block">
                            <i class="fas fa-plus"></i>Add Product
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php if (hasPermission('reports.view')): ?>
                    <div class="col-6 col-md-3">
                        <a href="<?= url('reports/daily') ?>" class="btn btn-outline btn-block">
                            <i class="fas fa-chart-bar"></i>Reports
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php if (hasPermission('transactions.view')): ?>
                    <div class="col-6 col-md-3">
                        <a href="<?= url('transactions') ?>" class="btn btn-outline btn-block">
                            <i class="fas fa-history"></i>History
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
