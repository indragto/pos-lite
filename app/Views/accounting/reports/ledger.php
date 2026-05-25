<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>View general ledger for an account</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('accounting/reports/export/ledger?<?= http_build_query(['coa_id' => $coaId ?? '', 'start_date' => $startDate ?? '', 'end_date' => $endDate ?? '']) ?>')" class="btn btn-outline"
           <?= empty($coaId) ? 'disabled' : '' ?>>
            <i class="fas fa-download"></i>Export
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="<?= url('accounting/reports/ledger') ?>" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Account</label>
                <select name="coa_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Select Account --</option>
                    <?php foreach ($accounts ?? [] as $acc): ?>
                        <option value="<?= $acc['id'] ?>"
                                <?= ($coaId ?? '') == $acc['id'] ? 'selected' : '' ?>>
                            <?= e($acc['code']) ?> - <?= e($acc['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">From</label>
                <input type="date" name="start_date" class="form-control"
                       value="<?= e($startDate ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">To</label>
                <input type="date" name="end_date" class="form-control"
                       value="<?= e($endDate ?? '') ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-filter"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($account)): ?>
    <!-- Account Info -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <small class="text-muted d-block">Account</small>
                    <strong class="text-primary"><?= e($account['code']) ?> - <?= e($account['name']) ?></strong>
                </div>
                <div class="col-md-2">
                    <small class="text-muted d-block">Type</small>
                    <?php
                    $typeBadge = match ($account['type'] ?? '') {
                        'asset' => 'badge-info',
                        'liability' => 'badge-warning',
                        'equity' => 'badge-primary',
                        'revenue' => 'badge-success',
                        'expense' => 'badge-danger',
                        default => 'badge-info',
                    };
                    ?>
                    <span class="badge <?= $typeBadge ?>"><?= e(ucfirst($account['type'] ?? '')) ?></span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Period</small>
                    <strong><?= formatDate($startDate ?? '', 'd/m/Y') ?> - <?= formatDate($endDate ?? '', 'd/m/Y') ?></strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Ledger Lines -->
    <div class="card">
        <div class="card-body p-0">
            <?php if (!empty($lines)): ?>
                <div class="table-responsive" style="border: none;">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Entry No</th>
                                <th>Description</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end">Credit</th>
                                <th class="text-end">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $runningBalance = 0;
                            $totalDebit = 0;
                            $totalCredit = 0;

                            // Opening balance row
                            $openingBalance = $account['opening_balance'] ?? 0;
                            $runningBalance = $openingBalance;
                            ?>
                            <tr class="table-light">
                                <td class="text-muted"><?= formatDate($startDate ?? '', 'd/m/Y') ?></td>
                                <td class="text-muted">-</td>
                                <td class="fw-bold">Opening Balance</td>
                                <td class="text-end">-</td>
                                <td class="text-end">-</td>
                                <td class="text-end fw-bold"><?= formatRupiah($openingBalance) ?></td>
                            </tr>

                            <?php foreach ($lines as $line):
                                $runningBalance += ($line['debit'] ?? 0) - ($line['credit'] ?? 0);
                                $totalDebit += (float)($line['debit'] ?? 0);
                                $totalCredit += (float)($line['credit'] ?? 0);
                            ?>
                            <tr>
                                <td><?= formatDate($line['date'] ?? '', 'd/m/Y') ?></td>
                                <td>
                                    <a href="<?= url('accounting/journal/' . ($line['entry_id'] ?? '')) ?>" class="text-primary text-decoration-none fw-bold">
                                        <?= e($line['entry_no'] ?? '') ?>
                                    </a>
                                </td>
                                <td><?= e($line['description'] ?? '') ?></td>
                                <td class="text-end"><?= formatRupiah($line['debit'] ?? 0) ?></td>
                                <td class="text-end"><?= formatRupiah($line['credit'] ?? 0) ?></td>
                                <td class="text-end fw-bold"><?= formatRupiah($runningBalance) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="3" class="fw-bold">Totals</td>
                                <td class="text-end fw-bold text-success"><?= formatRupiah($totalDebit) ?></td>
                                <td class="text-end fw-bold text-danger"><?= formatRupiah($totalCredit) ?></td>
                                <td class="text-end fw-bold"><?= formatRupiah($runningBalance) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h5>No Transactions</h5>
                    <p>No ledger entries found for this account in the selected period.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-book"></i>
        <h5>Select an Account</h5>
        <p>Choose a chart of account to view its ledger.</p>
    </div>
<?php endif; ?>
