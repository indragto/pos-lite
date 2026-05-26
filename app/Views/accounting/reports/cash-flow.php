<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>Cash inflows and outflows for the selected period</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('accounting/reports/export/cash-flow?' . http_build_query(['start_date' => $startDate ?? '', 'end_date' => $endDate ?? ''])) ?>" class="btn btn-outline">
            <i class="fas fa-download"></i>Export
        </a>
    </div>
</div>

<!-- Date Range Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="<?= url('accounting/reports/cash-flow') ?>" class="row g-3 align-items-end">
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

<?php if (!empty($cashFlow)): ?>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive" style="border: none;">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Account</th>
                            <th class="text-end">Inflows</th>
                            <th class="text-end">Outflows</th>
                            <th class="text-end">Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sumInflows = 0;
                        $sumOutflows = 0;
                        $sumNet = 0;

                        foreach ($cashFlow as $item):
                            $inflows = (float)($item['inflows'] ?? 0);
                            $outflows = (float)($item['outflows'] ?? 0);
                            $net = $inflows - $outflows;
                            $sumInflows += $inflows;
                            $sumOutflows += $outflows;
                            $sumNet += $net;
                        ?>
                        <tr>
                            <td>
                                <span class="text-primary fw-bold"><?= e($item['code'] ?? '') ?></span>
                                <?= e($item['name'] ?? '') ?>
                            </td>
                            <td class="text-end">
                                <span class="text-success fw-bold"><?= formatRupiah($inflows) ?></span>
                            </td>
                            <td class="text-end">
                                <span class="text-danger fw-bold"><?= formatRupiah($outflows) ?></span>
                            </td>
                            <td class="text-end">
                                <?php if ($net >= 0): ?>
                                    <span class="text-success fw-bold"><?= formatRupiah($net) ?></span>
                                <?php else: ?>
                                    <span class="text-danger fw-bold">(<?= formatRupiah(abs($net)) ?>)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td class="fw-bold fs-5">Summary</td>
                            <td class="text-end fw-bold text-success fs-5"><?= formatRupiah($sumInflows) ?></td>
                            <td class="text-end fw-bold text-danger fs-5"><?= formatRupiah($sumOutflows) ?></td>
                            <td class="text-end fw-bold <?= $sumNet >= 0 ? 'text-success' : 'text-danger' ?> fs-5">
                                <?php if ($sumNet >= 0): ?>
                                    <?= formatRupiah($sumNet) ?>
                                <?php else: ?>
                                    (<?= formatRupiah(abs($sumNet)) ?>)
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-center py-3">
                                <?php if ($sumNet >= 0): ?>
                                    <span class="badge badge-success fs-6">
                                        <i class="fas fa-arrow-up me-1"></i>Net Cash Inflow: <?= formatRupiah($sumNet) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-danger fs-6">
                                        <i class="fas fa-arrow-down me-1"></i>Net Cash Outflow: <?= formatRupiah(abs($sumNet)) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-money-bill-wave"></i>
        <h5>No Cash Flow Data</h5>
        <p>No cash flow entries found for the selected period.</p>
    </div>
<?php endif; ?>
