  <?php
/**
 * Monthly Report View
 * Variables: $title, $month, $year, $summary, $daily_sales, $top_products, $comparison
 */
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1"><i class="fas fa-calendar-alt me-2"></i>Monthly Report</h5>
            <p class="text-muted mb-0"><?= date('F Y', mktime(0, 0, 0, $month, 1, $year)) ?></p>
        </div>
        <div class="d-flex gap-2">
            <select class="form-select" id="monthSelect" style="min-height: 44px;"
                    onchange="window.location.href='?month='+this.value+'&year=<?= $year ?>'">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= $m == $month ? 'selected' : '' ?>>
                    <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                </option>
                <?php endfor; ?>
            </select>
            <select class="form-select" id="yearSelect" style="min-height: 44px; max-width: 100px;"
                    onchange="window.location.href='?month=<?= $month ?>&year='+this.value">
                <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <button class="btn btn-outline-primary" onclick="window.print()" style="min-height: 44px;">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 56px; height: 56px;">
                        <i class="fas fa-chart-line fa-lg text-primary"></i>
                    </div>
                    <h6 class="text-muted mb-1">Total Revenue</h6>
                    <h4 class="fw-bold mb-0"><?= formatRupiah($summary['total_revenue'] ?? 0) ?></h4>
                    <?php if (!empty($comparison['revenue_change'])): ?>
                    <small class="<?= $comparison['revenue_change'] >= 0 ? 'text-success' : 'text-danger' ?>">
                        <i class="fas fa-arrow-<?= $comparison['revenue_change'] >= 0 ? 'up' : 'down' ?>"></i>
                        <?= number_format(abs($comparison['revenue_change']), 1) ?>% vs last month
                    </small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 56px; height: 56px;">
                        <i class="fas fa-shopping-cart fa-lg text-success"></i>
                    </div>
                    <h6 class="text-muted mb-1">Transactions</h6>
                    <h4 class="fw-bold mb-0"><?= number_format($summary['total_transactions'] ?? 0) ?></h4>
                    <?php if (!empty($comparison['transaction_change'])): ?>
                    <small class="<?= $comparison['transaction_change'] >= 0 ? 'text-success' : 'text-danger' ?>">
                        <i class="fas fa-arrow-<?= $comparison['transaction_change'] >= 0 ? 'up' : 'down' ?>"></i>
                        <?= number_format(abs($comparison['transaction_change']), 1) ?>% vs last month
                    </small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 56px; height: 56px;">
                        <i class="fas fa-calendar-check fa-lg text-info"></i>
                    </div>
                    <h6 class="text-muted mb-1">Active Days</h6>
                    <h4 class="fw-bold mb-0"><?= number_format($summary['active_days'] ?? 0) ?></h4>
                    <small class="text-muted">of <?= date('t', mktime(0, 0, 0, $month, 1, $year)) ?> days</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 56px; height: 56px;">
                        <i class="fas fa-calculator fa-lg text-warning"></i>
                    </div>
                    <h6 class="text-muted mb-1">Daily Average</h6>
                    <h4 class="fw-bold mb-0"><?= formatRupiah($summary['daily_average'] ?? 0) ?></h4>
                    <small class="text-muted">per active day</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Sales Table -->
    <div class="row g-3">
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-calendar-day me-2"></i>Daily Breakdown</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Date</th>
                                    <th class="text-center">Transactions</th>
                                    <th class="text-center">Items</th>
                                    <th class="text-end pe-3">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($daily_sales)): ?>
                                    <?php foreach ($daily_sales as $day): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <span class="fw-semibold"><?= date('d M Y', strtotime($day['date'])) ?></span>
                                            <br><small class="text-muted"><?= date('l', strtotime($day['date'])) ?></small>
                                        </td>
                                        <td class="text-center"><?= number_format($day['transactions']) ?></td>
                                        <td class="text-center"><?= number_format($day['items']) ?></td>
                                        <td class="text-end pe-3 fw-bold"><?= formatRupiah($day['revenue']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="fas fa-calendar-times fa-2x mb-2 d-block"></i>
                                            No sales data for this month
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
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-trophy me-2 text-warning"></i>Top 5 Products</h6>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($top_products)): ?>
                        <?php $rank = 1; foreach ($top_products as $product): ?>
                        <div class="d-flex align-items-center px-3 py-3 <?= $rank > 1 ? 'border-top' : '' ?>">
                            <div class="me-3">
                                <span class="badge rounded-circle d-flex align-items-center justify-content-center
                                             <?= $rank === 1 ? 'bg-warning text-dark' : ($rank === 2 ? 'bg-secondary text-white' : 'bg-light text-dark') ?>"
                                      style="width: 36px; height: 36px;">
                                    <?= $rank ?>
                                </span>
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="fw-semibold text-truncate"><?= e($product['name']) ?></div>
                                <small class="text-muted"><?= number_format($product['qty']) ?> sold</small>
                            </div>
                            <div class="text-end">
                                <strong><?= formatRupiah($product['revenue']) ?></strong>
                            </div>
                        </div>
                        <?php $rank++; endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center py-4">No product data</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>