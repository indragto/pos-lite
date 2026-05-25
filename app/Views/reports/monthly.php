<?php
$sel = $selectedMonth ?? date('Y-m');
$monthNum = (int)date('m', strtotime($sel . '-01'));
$yearNum = (int)date('Y', strtotime($sel . '-01'));
$monthLabel = date('F Y', mktime(0, 0, 0, $monthNum, 1, $yearNum));
?>

<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p><?= e($monthLabel) ?></p>
    </div>
    <div class="d-flex gap-2">
        <form method="GET" action="" class="d-flex gap-2">
            <input type="month" class="form-control" name="month" value="<?= e($sel) ?>">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>Go
            </button>
        </form>
        <button class="btn btn-outline" onclick="window.print()">
            <i class="fas fa-print"></i>Print
        </button>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon primary"><i class="fas fa-chart-line"></i></div>
            <div class="stat-content">
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value"><?= formatRupiah($summary['total_sales'] ?? 0) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon success"><i class="fas fa-shopping-cart"></i></div>
            <div class="stat-content">
                <div class="stat-label">Transactions</div>
                <div class="stat-value"><?= number_format($summary['total_transactions'] ?? 0) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon info"><i class="fas fa-calculator"></i></div>
            <div class="stat-content">
                <div class="stat-label">Avg Transaction</div>
                <div class="stat-value"><?= formatRupiah($summary['avg_transaction'] ?? 0) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon warning"><i class="fas fa-percent"></i></div>
            <div class="stat-content">
                <div class="stat-label">Total Tax</div>
                <div class="stat-value"><?= formatRupiah($summary['total_tax'] ?? 0) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Payment Method Breakdown -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-wallet text-success me-2"></i>Payment Methods</h6>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Method</th>
                            <th class="text-center">Count</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($paymentMethods)): ?>
                            <?php foreach ($paymentMethods as $pm): ?>
                            <?php
                                $pmBadge = match ($pm['payment_method'] ?? '') {
                                    'cash' => 'badge-success',
                                    'card' => 'badge-primary',
                                    'qris' => 'badge-info',
                                    default => 'badge-warning',
                                };
                            ?>
                            <tr>
                                <td>
                                    <span class="badge <?= $pmBadge ?>"><?= ucfirst($pm['payment_method']) ?></span>
                                </td>
                                <td class="text-center"><?= number_format($pm['count']) ?></td>
                                <td class="text-end fw-bold"><?= formatRupiah($pm['total']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">
                                    <div class="empty-state">
                                        <i class="fas fa-wallet"></i>
                                        <h5>No payment data</h5>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Daily Sales -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-calendar-day text-primary me-2"></i>Daily Sales</h6>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th class="text-center">Transactions</th>
                            <th class="text-end">Daily Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($dailySales)): ?>
                            <?php foreach ($dailySales as $day): ?>
                            <tr>
                                <td class="fw-bold"><?= formatDate($day['date'], 'd/m/Y') ?></td>
                                <td class="text-center">
                                    <span class="badge badge-primary"><?= number_format($day['transaction_count']) ?></span>
                                </td>
                                <td class="text-end fw-bold"><?= formatRupiah($day['daily_total']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">
                                    <div class="empty-state">
                                        <i class="fas fa-calendar-times"></i>
                                        <h5>No sales this month</h5>
                                        <p>No transaction data found for <?= e($monthLabel) ?></p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Export -->
<div class="d-flex justify-content-end">
    <a href="<?= url('reports/monthly') ?>?month=<?= e($sel) ?>&export=1" class="btn btn-outline">
        <i class="fas fa-file-csv"></i>Export CSV
    </a>
</div>
