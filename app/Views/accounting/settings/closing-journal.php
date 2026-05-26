<div class="page-header">
    <div><h1><i class="fas fa-lock text-warning me-2"></i>Closing Journal (Tutup Buku)</h1><p>Close revenue and expense accounts for a period</p></div>
</div>

<div class="card mb-4"><div class="card-body">
    <form method="get" class="row g-3 align-items-end">
        <div class="col-md-4"><label class="form-label">Period</label><input type="month" name="period" class="form-control" value="<?= e($period) ?>"></div>
        <div class="col-md-2"><button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i>Preview</button></div>
    </form>
</div></div>

<?php if (!empty($revenues) || !empty($expenses)): ?>
<div class="row g-3">
    <div class="col-md-6"><div class="card"><div class="card-header"><h6 class="mb-0"><i class="fas fa-arrow-up text-success me-2"></i>Revenue Accounts</h6></div>
        <div class="table-responsive"><table class="table"><thead><tr><th>Account</th><th class="text-end">Balance</th></tr></thead><tbody>
        <?php foreach ($revenues as $r): ?><tr><td><?= e($r['code']) ?> - <?= e($r['name']) ?></td><td class="text-end fw-bold text-success"><?= formatRupiah($r['balance']) ?></td></tr><?php endforeach; ?>
        <tr style="background:var(--gray-50)"><td class="fw-bold">Total Revenue</td><td class="text-end fw-bold text-success"><?= formatRupiah($totalRevenue) ?></td></tr>
        </tbody></table></div>
    </div></div>
    <div class="col-md-6"><div class="card"><div class="card-header"><h6 class="mb-0"><i class="fas fa-arrow-down text-danger me-2"></i>Expense Accounts</h6></div>
        <div class="table-responsive"><table class="table"><thead><tr><th>Account</th><th class="text-end">Balance</th></tr></thead><tbody>
        <?php foreach ($expenses as $e): ?><tr><td><?= e($e['code']) ?> - <?= e($e['name']) ?></td><td class="text-end fw-bold text-danger"><?= formatRupiah($e['balance']) ?></td></tr><?php endforeach; ?>
        <tr style="background:var(--gray-50)"><td class="fw-bold">Total Expense</td><td class="text-end fw-bold text-danger"><?= formatRupiah($totalExpense) ?></td></tr>
        </tbody></table></div>
    </div></div>
</div>

<div class="card mt-3"><div class="card-body p-4 text-center">
    <h4 class="mb-3">Net Income: <span class="<?= $netIncome >= 0 ? 'text-success' : 'text-danger' ?>"><?= formatRupiah($netIncome) ?></span></h4>
    <form method="post" action="<?= url('accounting/closing-journal/save') ?>" class="row g-3 justify-content-center align-items-end"><?= csrf_field() ?>
        <input type="hidden" name="period" value="<?= e($period) ?>">
        <div class="col-md-4"><label class="form-label">Retained Earnings Account</label>
            <select name="retained_earnings_account" class="form-select" required><option value="">-- Select --</option>
            <?php foreach ($coaOptions as $id => $name): ?><option value="<?= $id ?>" <?= $id == 32 ? 'selected' : '' ?>><?= e($name) ?></option><?php endforeach; ?>
            </select></div>
        <div class="col-md-2"><button type="submit" class="btn btn-warning w-100" onclick="return confirm('Post closing journal? This will zero out all revenue and expense accounts for <?= date('F Y', strtotime($period . '-01')) ?>')"><i class="fas fa-lock"></i>Close Books</button></div>
    </form>
</div></div>
<?php else: ?>
<div class="empty-state"><i class="fas fa-check-circle"></i><h5>No Revenue or Expense to Close</h5><p>All temporary accounts are already zero for this period.</p></div>
<?php endif; ?>
