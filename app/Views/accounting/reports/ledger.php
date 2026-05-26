<div class="page-header">
    <div>
        <h1><i class="fas fa-book text-primary me-2"></i>General Ledger</h1>
        <p>View ledger transactions for any account</p>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="<?= url('accounting/ledger') ?>" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Account</label>
                <select name="coa_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Select Account --</option>
                    <?php foreach ($accounts as $id => $name): ?>
                        <option value="<?= $id ?>" <?= ($coaId ?? '') == $id ? 'selected' : '' ?>>
                            <?= e($name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">From</label>
                <input type="date" name="start_date" class="form-control" value="<?= e($startDate ?? date('Y-m-01')) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">To</label>
                <input type="date" name="end_date" class="form-control" value="<?= e($endDate ?? date('Y-m-d')) ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($lines)): ?>
<div class="card mb-3">
    <div class="card-body py-2">
        <span class="fw-bold"><?= e($account['code'] ?? '') ?> - <?= e($account['name'] ?? '') ?></span>
        <span class="badge badge-<?= ($account['type'] ?? '') === 'asset' ? 'info' : (($account['type'] ?? '') === 'liability' ? 'warning' : 'primary') ?> ms-2"><?= e(ucfirst($account['type'] ?? '')) ?></span>
        <span class="text-muted ms-3"><?= formatDate($startDate ?? '', 'd/m/Y') ?> - <?= formatDate($endDate ?? '', 'd/m/Y') ?></span>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Date</th><th>Entry No</th><th>Description</th><th class="text-end">Debit</th><th class="text-end">Credit</th><th class="text-end">Balance</th></tr></thead>
            <tbody>
                <?php
                $running = 0; $tDeb = 0; $tCred = 0;
                if ($openingBalance > 0) $running = $openingBalance;
                elseif ($openingBalance < 0) $running = $openingBalance;
                ?>
                <?php if ($openingBalance != 0): ?>
                <tr style="background:var(--gray-50)"><td><?= formatDate($startDate, 'd/m/Y') ?></td><td class="text-muted">-</td><td class="fw-bold">Opening Balance</td><td class="text-end">-</td><td class="text-end">-</td><td class="text-end fw-bold"><?= formatRupiah($openingBalance) ?></td></tr>
                <?php endif; ?>
                <?php foreach ($lines as $line):
                    $running += ($line['debit'] ?? 0) - ($line['credit'] ?? 0);
                    $tDeb += (float)($line['debit'] ?? 0); $tCred += (float)($line['credit'] ?? 0);
                ?>
                <tr>
                    <td><?= formatDate($line['date'] ?? '', 'd/m/Y') ?></td>
                    <td><a href="<?= url('accounting/journal/' . ($line['entry_id'] ?? 0)) ?>" class="fw-bold"><?= e($line['entry_no'] ?? '') ?></a></td>
                    <td><?= e($line['description'] ?? '') ?></td>
                    <td class="text-end"><?= formatRupiah($line['debit'] ?? 0) ?></td>
                    <td class="text-end"><?= formatRupiah($line['credit'] ?? 0) ?></td>
                    <td class="text-end fw-bold"><?= formatRupiah($running) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot><tr style="background:var(--gray-50)"><td colspan="3" class="fw-bold">Totals</td><td class="text-end fw-bold"><?= formatRupiah($tDeb) ?></td><td class="text-end fw-bold"><?= formatRupiah($tCred) ?></td><td class="text-end fw-bold"><?= formatRupiah($running) ?></td></tr></tfoot>
        </table>
    </div>
</div>
<?php elseif ($coaId): ?>
<div class="empty-state"><i class="fas fa-inbox"></i><h5>No Transactions</h5><p>No entries for this account in the selected period.</p></div>
<?php else: ?>
<div class="empty-state"><i class="fas fa-book-open"></i><h5>Select an Account</h5><p>Choose an account from the dropdown above to view its ledger.</p></div>
<?php endif; ?>
