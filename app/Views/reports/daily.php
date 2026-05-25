  <?php
/**
 * Daily Report View
 * Variables: $title, $date, $summary, $hourly_sales, $payment_breakdown
 */
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1"><i class="fas fa-calendar-day me-2"></i>Daily Report</h5>
            <p class="text-muted mb-0">Sales report for <?= formatDate($date) ?></p>
        </div>
        <div class="d-flex gap-2">
            <input type="date" class="form-control" id="reportDate" value="<?= date('Y-m-d', strtotime($date)) ?>"
                   onchange="window.location.href='?date='+this.value" style="min-height: 44px;">
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
                        <i class="fas fa-money-bill-wave fa-lg text-primary"></i>
                    </div>
                    <h6 class="text-muted mb-1">Total Sales</h6>
                    <h4 class="fw-bold mb-0"><?= formatRupiah($summary['total_sales'] ?? 0) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 56px; height: 56px;">
                        <i class="fas fa-receipt fa-lg text-success"></i>
                    </div>
                    <h6 class="text-muted mb-1">Transactions</h6>
                    <h4 class="fw-bold mb-0"><?= number_format($summary['transaction_count'] ?? 0) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 56px; height: 56px;">
                        <i class="fas fa-box fa-lg text-info"></i>
                    </div>
                    <h6 class="text-muted mb-1">Items Sold</h6>
                    <h4 class="fw-bold mb-0"><?= number_format($summary['total_items'] ?? 0) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 56px; height: 56px;">
                        <i class="fas fa-percent fa-lg text-warning"></i>
                    </div>
                    <h6 class="text-muted mb-1">Avg. Transaction</h6>
                    <h4 class="fw-bold mb-0"><?= formatRupiah($summary['average_sale'] ?? 0) ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Breakdown & Hourly Sales -->
    <div class="row g-3">
        <div class="col-12 col-md-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-wallet me-2"></i>Payment Breakdown</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($payment_breakdown)): ?>
                        <?php foreach ($payment_breakdown as $idx => $payment): ?>
                        <div class="d-flex align-items-center justify-content-between py-3 <?= $idx > 0 ? 'border-top' : '' ?>">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3
                                            <?= $payment['method'] === 'cash' ? 'bg-success' : '' ?>
                                            <?= $payment['method'] === 'card' ? 'bg-primary' : '' ?>
                                            <?= $payment['method'] === 'qris' ? 'bg-info' : '' ?>"
                                     style="width: 44px; height: 44px;">
                                    <i class="fas fa-<?= $payment['method'] === 'cash' ? 'money-bill' : ($payment['method'] === 'card' ? 'credit-card' : 'qrcode') ?> text-white"></i>
                                </div>
                                <div>
                                    <strong><?= ucfirst($payment['method']) ?></strong>
                                    <br><small class="text-muted"><?= (int)$payment['count'] ?> transactions</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <strong><?= formatRupiah($payment['total']) ?></strong>
                                <br><small class="text-muted"><?= number_format($payment['percentage'] ?? 0, 1) ?>%</small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">No payment data</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2"></i>Hourly Sales</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Hour</th>
                                    <th class="text-center">Transactions</th>
                                    <th class="text-center">Items</th>
                                    <th class="text-end pe-3">Sales</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($hourly_sales)): ?>
                                    <?php foreach ($hourly_sales as $hour): ?>
                                    <?php if ($hour['sales'] > 0): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <span class="badge bg-light text-dark"><?= sprintf('%02d:00 - %02d:59', $hour['hour'], $hour['hour']) ?></span>
                                        </td>
                                        <td class="text-center"><?= number_format($hour['transactions']) ?></td>
                                        <td class="text-center"><?= number_format($hour['items']) ?></td>
                                        <td class="text-end pe-3 fw-bold"><?= formatRupiah($hour['sales']) ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="fas fa-chart-bar fa-2x mb-2 d-block"></i>
                                            No hourly data available
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>