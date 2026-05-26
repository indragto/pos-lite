<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>Verify that total debits equal total credits</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('accounting/reports/export/trial-balance?' . http_build_query(['end_date' => $endDate ?? ''])) ?>" class="btn btn-outline">
            <i class="fas fa-download"></i>Export
        </a>
    </div>
</div>

<!-- End Date Selector -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="<?= url('accounting/reports/trial-balance') ?>" class="row g-3 align-items-end">
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

<?php if (!empty($accounts)): ?>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive" style="border: none;">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Account Code</th>
                            <th>Account Name</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $currentType = '';
                        $totalDebit = 0;
                        $totalCredit = 0;

                        foreach ($accounts as $acc):
                            // Type header
                            if ($currentType !== ($acc['type'] ?? '')):
                                $currentType = $acc['type'] ?? '';
                                $typeLabel = match ($currentType) {
                                    'asset' => 'Assets',
                                    'liability' => 'Liabilities',
                                    'equity' => 'Equity',
                                    'revenue' => 'Revenue',
                                    'expense' => 'Expenses',
                                    default => ucfirst($currentType),
                                };
                                $typeBadge = match ($currentType) {
                                    'asset' => 'badge-info',
                                    'liability' => 'badge-warning',
                                    'equity' => 'badge-primary',
                                    'revenue' => 'badge-success',
                                    'expense' => 'badge-danger',
                                    default => 'badge-info',
                                };
                        ?>
                            <tr class="table-light">
                                <td colspan="4">
                                    <span class="badge <?= $typeBadge ?> me-2"><?= e($typeLabel) ?></span>
                                </td>
                            </tr>
                        <?php
                            endif;

                            $debitBal = (float)($acc['debit_balance'] ?? 0);
                            $creditBal = (float)($acc['credit_balance'] ?? 0);
                            $totalDebit += $debitBal;
                            $totalCredit += $creditBal;
                        ?>
                        <tr>
                            <td>
                                <span class="text-primary fw-bold"><?= e($acc['code'] ?? '') ?></span>
                            </td>
                            <td><?= e($acc['name'] ?? '') ?></td>
                            <td class="text-end <?= $debitBal > 0 ? 'fw-bold' : '' ?>">
                                <?= $debitBal > 0 ? formatRupiah($debitBal) : '-' ?>
                            </td>
                            <td class="text-end <?= $creditBal > 0 ? 'fw-bold' : '' ?>">
                                <?= $creditBal > 0 ? formatRupiah($creditBal) : '-' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="2" class="fw-bold fs-5">Totals</td>
                            <td class="text-end fw-bold text-success fs-5"><?= formatRupiah($totalDebit) ?></td>
                            <td class="text-end fw-bold text-danger fs-5"><?= formatRupiah($totalCredit) ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-center py-3">
                                <?php if (abs($totalDebit - $totalCredit) < 0.01): ?>
                                    <span class="badge badge-success fs-6">
                                        <i class="fas fa-check-circle me-1"></i>Balanced — Debits equal Credits
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-danger fs-6">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Out of balance by <?= formatRupiah(abs($totalDebit - $totalCredit)) ?>
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
        <i class="fas fa-balance-scale"></i>
        <h5>No Data Available</h5>
        <p>No accounts have balances for the selected date.</p>
    </div>
<?php endif; ?>
