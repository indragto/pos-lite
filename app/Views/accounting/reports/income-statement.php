<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>Revenue and expenses for the selected period</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('accounting/reports/export/income-statement?' . http_build_query(['start_date' => $startDate ?? '', 'end_date' => $endDate ?? ''])) ?>" class="btn btn-outline">
            <i class="fas fa-download"></i>Export
        </a>
    </div>
</div>

<!-- Date Range Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="<?= url('accounting/reports/income-statement') ?>" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">From</label>
                <input type="date" name="start_date" class="form-control"
                       value="<?= e($startDate ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">To</label>
                <input type="date" name="end_date" class="form-control"
                       value="<?= e($endDate ?? '') ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sync-alt"></i>Generate
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($revenues) || !empty($expenses)): ?>
    <div class="row g-3">
        <!-- Revenue Section -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6><i class="fas fa-arrow-trend-up text-success me-2"></i>Revenue</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="border: none;">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <?php foreach ($revenues as $acc): ?>
                                <tr>
                                    <td><?= e($acc['code']) ?> - <?= e($acc['name']) ?></td>
                                    <td class="text-end fw-bold"><?= formatRupiah($acc['total'] ?? 0) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td class="fw-bold">Total Revenue</td>
                                    <td class="text-end fw-bold text-success"><?= formatRupiah($totalRevenue ?? 0) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expense Section -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6><i class="fas fa-arrow-trend-down text-danger me-2"></i>Expenses</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="border: none;">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <?php foreach ($expenses as $acc): ?>
                                <tr>
                                    <td><?= e($acc['code']) ?> - <?= e($acc['name']) ?></td>
                                    <td class="text-end fw-bold"><?= formatRupiah($acc['total'] ?? 0) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td class="fw-bold">Total Expenses</td>
                                    <td class="text-end fw-bold text-danger"><?= formatRupiah($totalExpense ?? 0) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Net Income -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0 fw-bold">Net Income</h5>
                    <small class="text-muted">
                        <?= formatDate($startDate ?? '', 'd/m/Y') ?> - <?= formatDate($endDate ?? '', 'd/m/Y') ?>
                    </small>
                </div>
                <div class="col-md-6 text-end">
                    <?php
                    $netIncome = ($totalRevenue ?? 0) - ($totalExpense ?? 0);
                    $isProfit = $netIncome >= 0;
                    ?>
                    <span class="fs-4 fw-bold <?= $isProfit ? 'text-success' : 'text-danger' ?>">
                        <?= $isProfit ? '<i class="fas fa-arrow-up"></i>' : '<i class="fas fa-arrow-down"></i>' ?>
                        <?= formatRupiah(abs($netIncome)) ?>
                    </span>
                    <br>
                    <span class="badge <?= $isProfit ? 'badge-success' : 'badge-danger' ?>">
                        <?= $isProfit ? 'Profit' : 'Loss' ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-chart-pie"></i>
        <h5>No Income Statement Data</h5>
        <p>No revenue or expense entries found for the selected period.</p>
    </div>
<?php endif; ?>
