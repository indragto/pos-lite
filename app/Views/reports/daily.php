  <?php
/**
 * Daily Report View
 * Variables: $title, $selectedDate, $summary, $paymentMethods, $transactions
 */

$date = $selectedDate ?? date('Y-m-d');
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1"><i class="fas fa-calendar-day me-2"></i>Daily Report</h5>
            <p class="text-muted mb-0">Sales report for <?= formatDate($date) ?></p>
        </div>
        <div class="d-flex gap-2">
            <input type="date" class="form-control" id="reportDate" value="<?= $date ?>"
                   onchange="window.location.href='?date='+this.value" style="min-height: 44px;">
            <a href="<?= url('reports/export/daily?date=' . $date) ?>" class="btn btn-outline-primary" style="min-height: 44px;">
                <i class="fas fa-file-csv me-1"></i>Export
            </a>
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
                    <h4 class="fw-bold mb-0"><?= number_format($summary['total_transactions'] ?? 0) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 56px; height: 56px;">
                        <i class="fas fa-coins fa-lg text-info"></i>
                    </div>
                    <h6 class="text-muted mb-1">Avg. Transaction</h6>
                    <h4 class="fw-bold mb-0"><?= formatRupiah($summary['avg_transaction'] ?? 0) ?></h4>
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
                    <h6 class="text-muted mb-1">Total Tax</h6>
                    <h4 class="fw-bold mb-0"><?= formatRupiah($summary['total_tax'] ?? 0) ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Breakdown -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-0 py-3">
            <h6 class="mb-0 fw-bold"><i class="fas fa-wallet me-2"></i>Payment Method Breakdown</h6>
        </div>
        <div class="card-body">
            <?php if (!empty($paymentMethods)): ?>
            <div class="row g-3">
                <?php foreach ($paymentMethods as $pm): ?>
                <div class="col-4">
                    <div class="text-center p-3 rounded bg-light">
                        <i class="fas fa-<?= $pm['payment_method'] === 'cash' ? 'money-bill' : ($pm['payment_method'] === 'card' ? 'credit-card' : 'qrcode') ?> fa-2x mb-2 text-primary"></i>
                        <div class="fw-bold"><?= ucfirst($pm['payment_method']) ?></div>
                        <div class="text-muted small"><?= (int)$pm['count'] ?> txns</div>
                        <div class="fw-bold text-primary"><?= formatRupiah($pm['total']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <p class="text-muted text-center py-3">No payment data available</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 py-3">
            <h6 class="mb-0 fw-bold"><i class="fas fa-list me-2"></i>Transactions</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Invoice</th>
                            <th>Time</th>
                            <th>Cashier</th>
                            <th>Payment</th>
                            <th class="text-end pe-3">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($transactions)): ?>
                            <?php foreach ($transactions as $txn): ?>
                            <tr>
                                <td class="ps-3">
                                    <a href="<?= url('transactions/' . $txn['id']) ?>" class="fw-bold">
                                        <?= e($txn['invoice_no']) ?>
                                    </a>
                                </td>
                                <td><?= formatDate($txn['created_at'], 'H:i') ?></td>
                                <td><?= e($txn['cashier_name'] ?? '-') ?></td>
                                <td><span class="badge bg-<?= $txn['payment_method'] === 'cash' ? 'success' : ($txn['payment_method'] === 'card' ? 'primary' : 'info') ?>"><?= ucfirst($txn['payment_method']) ?></span></td>
                                <td class="text-end pe-3 fw-bold"><?= formatRupiah($txn['total']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    No transactions found for this date
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>