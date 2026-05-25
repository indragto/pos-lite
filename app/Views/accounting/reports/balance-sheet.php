<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>Assets, liabilities, and equity at a point in time</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('accounting/reports/export/balance-sheet?<?= http_build_query(['end_date' => $endDate ?? '']) ?>') ?>" class="btn btn-outline">
            <i class="fas fa-download"></i>Export
        </a>
    </div>
</div>

<!-- End Date Selector -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="<?= url('accounting/reports/balance-sheet') ?>" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">As of Date</label>
                <input type="date" name="end_date" class="form-control"
                       value="<?= e($endDate ?? date('Y-m-d')) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sync-alt"></i>Generate
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    <!-- Assets -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <h6><i class="fas fa-building text-info me-2"></i>Assets</h6>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($assets)): ?>
                    <div class="table-responsive" style="border: none;">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <?php foreach ($assets as $acc): ?>
                                <tr>
                                    <td><?= e($acc['code']) ?> - <?= e($acc['name']) ?></td>
                                    <td class="text-end fw-bold"><?= formatRupiah($acc['balance'] ?? 0) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td class="fw-bold">Total Assets</td>
                                    <td class="text-end fw-bold text-success"><?= formatRupiah($totalAssets ?? 0) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state py-4">
                        <i class="fas fa-inbox"></i>
                        <p>No asset accounts found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Liabilities -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <h6><i class="fas fa-hand-holding-usd text-warning me-2"></i>Liabilities</h6>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($liabilities)): ?>
                    <div class="table-responsive" style="border: none;">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <?php foreach ($liabilities as $acc): ?>
                                <tr>
                                    <td><?= e($acc['code']) ?> - <?= e($acc['name']) ?></td>
                                    <td class="text-end fw-bold"><?= formatRupiah($acc['balance'] ?? 0) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td class="fw-bold">Total Liabilities</td>
                                    <td class="text-end fw-bold text-warning"><?= formatRupiah($totalLiabilities ?? 0) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state py-4">
                        <i class="fas fa-inbox"></i>
                        <p>No liability accounts found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Equity -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <h6><i class="fas fa-user-tie text-primary me-2"></i>Equity</h6>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($equity)): ?>
                    <div class="table-responsive" style="border: none;">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <?php foreach ($equity as $acc): ?>
                                <tr>
                                    <td><?= e($acc['code']) ?> - <?= e($acc['name']) ?></td>
                                    <td class="text-end fw-bold"><?= formatRupiah($acc['balance'] ?? 0) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <!-- Net Income -->
                                <tr class="table-light">
                                    <td class="fw-bold">Net Income (Current Period)</td>
                                    <td class="text-end fw-bold <?= ($netIncome ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= formatRupiah($netIncome ?? 0) ?>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td class="fw-bold">Total Equity</td>
                                    <td class="text-end fw-bold text-primary">
                                        <?= formatRupiah(($totalEquity ?? 0) + ($netIncome ?? 0)) ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state py-4">
                        <i class="fas fa-inbox"></i>
                        <p>No equity accounts found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Validation Row -->
<div class="card mt-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-4 text-center">
                <small class="text-muted d-block">Total Assets</small>
                <span class="fs-5 fw-bold text-success"><?= formatRupiah($totalAssets ?? 0) ?></span>
            </div>
            <div class="col-md-4 text-center">
                <small class="text-muted d-block">Liabilities + Equity</small>
                <span class="fs-5 fw-bold text-primary">
                    <?= formatRupiah(($totalLiabilities ?? 0) + ($totalEquity ?? 0) + ($netIncome ?? 0)) ?>
                </span>
            </div>
            <div class="col-md-4 text-center">
                <?php
                $lhs = $totalAssets ?? 0;
                $rhs = ($totalLiabilities ?? 0) + ($totalEquity ?? 0) + ($netIncome ?? 0);
                $balanced = abs($lhs - $rhs) < 0.01;
                ?>
                <?php if ($balanced): ?>
                    <span class="badge badge-success fs-6">
                        <i class="fas fa-check-circle me-1"></i>Balanced
                    </span>
                <?php else: ?>
                    <span class="badge badge-danger fs-6">
                        <i class="fas fa-exclamation-triangle me-1"></i>Out of balance by <?= formatRupiah(abs($lhs - $rhs)) ?>
                    </span>
                <?php endif; ?>
                <br>
                <small class="text-muted">Assets = Liabilities + Equity</small>
            </div>
        </div>
    </div>
</div>
