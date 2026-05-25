<div class="page-header">
    <div><h1>Transactions</h1><p>Sales history</p></div>
    <div class="view-toggle">
        <button class="btn btn-outline view-toggle-btn active" data-view="list" data-container="viewContainer"><i class="fas fa-list"></i></button>
        <button class="btn btn-outline view-toggle-btn" data-view="grid" data-container="viewContainer"><i class="fas fa-th-large"></i></button>
    </div>
</div>

<form method="GET" action="<?= url('transactions') ?>" class="toolbar mb-4">
    <input type="date" class="form-control" name="start_date" value="<?= e($startDate ?? '') ?>" style="min-width:160px;">
    <input type="date" class="form-control" name="end_date" value="<?= e($endDate ?? '') ?>" style="min-width:160px;">
    <select class="form-select" name="user_id"><option value="">All Cashiers</option><?php foreach ($users ?? [] as $u): ?><option value="<?= $u['id'] ?>" <?= ($userId ?? '') == $u['id'] ? 'selected' : '' ?>><?= e($u['full_name']) ?></option><?php endforeach; ?></select>
    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i>Filter</button>
</form>

<div id="viewContainer">
    <div class="view-list"><div class="card"><div class="table-responsive">
        <table class="table"><thead><tr><th>Invoice</th><th>Date</th><th>Cashier</th><th>Payment</th><th class="text-end">Total</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
        <tbody><?php if (!empty($transactions)): foreach ($transactions as $txn): ?>
        <tr><td><a href="<?= url('transactions/'.$txn['id']) ?>" class="fw-bold"><?= e($txn['invoice_no']) ?></a></td><td><?= formatDate($txn['created_at'],'d/m/Y H:i') ?></td><td><?= e($txn['cashier_name'] ?? '—') ?></td>
        <td><span class="badge badge-<?= $txn['payment_method']==='cash'?'success':($txn['payment_method']==='card'?'primary':'info') ?>"><?= ucfirst($txn['payment_method']) ?></span></td>
        <td class="text-end fw-bold"><?= formatRupiah($txn['total']) ?></td><td><span class="badge <?= $txn['status']==='completed'?'badge-success':'badge-danger' ?>"><?= ucfirst($txn['status']) ?></span></td>
        <td><div class="btn-group-actions justify-content-end"><a href="<?= url('transactions/'.$txn['id']) ?>" class="btn btn-sm btn-outline"><i class="fas fa-eye"></i></a><a href="<?= url('transactions/receipt/'.$txn['id']) ?>" class="btn btn-sm btn-outline" target="_blank"><i class="fas fa-print"></i></a><?php if ($txn['status']==='completed' && hasPermission('transactions.delete')): ?><form action="<?= url('transactions/void/'.$txn['id']) ?>" method="POST" onsubmit="return confirm('Void?')"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-ban"></i></button></form><?php endif; ?></div></td></tr>
        <?php endforeach; else: ?><tr><td colspan="7"><div class="empty-state"><i class="fas fa-receipt"></i><h5>No transactions</h5></div></td></tr><?php endif; ?></tbody>
    </table></div></div></div>
    <div class="view-grid"><?php if (!empty($transactions)): foreach ($transactions as $txn): ?>
    <div class="grid-card"><h5><?= e($txn['invoice_no']) ?></h5><p><?= formatDate($txn['created_at'],'d/m/Y H:i') ?> · <?= e($txn['cashier_name'] ?? '—') ?></p>
        <div class="grid-meta"><span class="badge badge-<?= $txn['payment_method']==='cash'?'success':($txn['payment_method']==='card'?'primary':'info') ?>"><?= ucfirst($txn['payment_method']) ?></span><span class="badge <?= $txn['status']==='completed'?'badge-success':'badge-danger' ?>"><?= ucfirst($txn['status']) ?></span><span class="badge badge-primary"><?= formatRupiah($txn['total']) ?></span></div>
        <div class="grid-actions"><a href="<?= url('transactions/'.$txn['id']) ?>" class="btn btn-sm btn-outline"><i class="fas fa-eye"></i></a><a href="<?= url('transactions/receipt/'.$txn['id']) ?>" class="btn btn-sm btn-outline" target="_blank"><i class="fas fa-print"></i></a></div></div>
    <?php endforeach; else: ?><div class="empty-state" style="grid-column:1/-1;"><i class="fas fa-receipt"></i><h5>No transactions</h5></div><?php endif; ?></div>
</div>
