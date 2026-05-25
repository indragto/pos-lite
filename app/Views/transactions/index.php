<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>Transaction history and management</p>
    </div>
    <?php if (hasPermission('transactions.pos')): ?>
    <a href="<?= url('pos') ?>" class="btn btn-success">
        <i class="fas fa-shopping-cart"></i>New Sale
    </a>
    <?php endif; ?>
</div>

<!-- Toolbar -->
<form method="GET" action="<?= url('transactions') ?>" class="toolbar mb-4">
    <input type="date" class="form-control" name="start_date" value="<?= e($startDate) ?>"
           placeholder="From date">
    <input type="date" class="form-control" name="end_date" value="<?= e($endDate) ?>"
           placeholder="To date">
    <select class="form-select" name="user_id">
        <option value="">All Cashiers</option>
        <?php foreach ($users as $user): ?>
        <option value="<?= $user['id'] ?>" <?= ($userId ?? '') == $user['id'] ? 'selected' : '' ?>>
            <?= e($user['full_name']) ?>
        </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-filter"></i>Filter
    </button>
    <a href="<?= url('transactions') ?>" class="btn btn-outline">
        <i class="fas fa-undo"></i>Reset
    </a>
</form>

<!-- Transactions Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Date</th>
                    <th>Cashier</th>
                    <th>Payment</th>
                    <th class="text-end">Total</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($transactions)): ?>
                    <?php foreach ($transactions as $txn): ?>
                    <?php
                        $methodBadge = match ($txn['payment_method'] ?? '') {
                            'cash' => 'badge-success',
                            'card' => 'badge-primary',
                            'qris' => 'badge-info',
                            default => 'badge-warning',
                        };
                        $methodIcon = match ($txn['payment_method'] ?? '') {
                            'cash' => 'fa-money-bill',
                            'card' => 'fa-credit-card',
                            'qris' => 'fa-qrcode',
                            default => 'fa-wallet',
                        };
                        $statusBadge = ($txn['status'] ?? '') === 'voided' ? 'badge-danger' : 'badge-success';
                    ?>
                    <tr>
                        <td>
                            <a href="<?= url("transactions/{$txn['id']}") ?>" class="fw-bold text-decoration-none">
                                <?= e($txn['invoice_no']) ?>
                            </a>
                        </td>
                        <td><?= formatDate($txn['created_at'], 'd/m/Y H:i') ?></td>
                        <td><?= e($txn['cashier_name'] ?? '—') ?></td>
                        <td>
                            <span class="badge <?= $methodBadge ?>">
                                <i class="fas <?= $methodIcon ?> me-1"></i><?= ucfirst($txn['payment_method']) ?>
                            </span>
                        </td>
                        <td class="text-end fw-bold"><?= formatRupiah($txn['total']) ?></td>
                        <td>
                            <span class="badge <?= $statusBadge ?>">
                                <?= ucfirst($txn['status'] ?? 'completed') ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group-actions justify-content-end">
                                <a href="<?= url("transactions/{$txn['id']}") ?>"
                                   class="btn btn-sm btn-outline" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= url("transactions/receipt/{$txn['id']}") ?>" target="_blank"
                                   class="btn btn-sm btn-outline" title="Receipt">
                                    <i class="fas fa-print"></i>
                                </a>
                                <?php if (hasPermission('transactions.delete') && ($txn['status'] ?? '') !== 'voided'): ?>
                                <button type="button" class="btn btn-sm btn-danger" title="Void"
                                        data-bs-toggle="modal" data-bs-target="#voidModal<?= $txn['id'] ?>">
                                    <i class="fas fa-ban"></i>
                                </button>

                                <!-- Void Modal -->
                                <div class="modal fade" id="voidModal<?= $txn['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"><i class="fas fa-ban text-danger me-2"></i>Void Transaction</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?= url("transactions/void/{$txn['id']}") ?>" method="POST">
                                                <?= csrf_field() ?>
                                                <div class="modal-body">
                                                    <p>Void invoice <strong><?= e($txn['invoice_no']) ?></strong>?</p>
                                                    <div class="mb-3">
                                                        <label class="form-label">Void Reason <span class="text-danger">*</span></label>
                                                        <textarea class="form-control" name="void_reason" rows="3" required
                                                                  placeholder="Enter reason for voiding"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-ban"></i>Void
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-receipt"></i>
                                <h5>No transactions found</h5>
                                <p>Try adjusting your date range or filters</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (($totalPages ?? 1) > 1): ?>
    <div class="card-footer">
        <nav>
            <ul class="pagination mb-0">
                <?php
                $txnParams = [];
                if ($startDate ?? '') $txnParams['start_date'] = $startDate;
                if ($endDate ?? '') $txnParams['end_date'] = $endDate;
                if ($userId ?? '') $txnParams['user_id'] = $userId;
                $txnQs = http_build_query($txnParams);
                $txBase = url('transactions') . ($txnQs ? '?' . $txnQs . '&' : '?');
                ?>
                <?php if (($page ?? 1) > 1): ?>
                <li><a href="<?= $txBase ?>page=<?= ($page ?? 1) - 1 ?>"><i class="fas fa-chevron-left"></i></a></li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == 1 || $i == $totalPages || abs($i - ($page ?? 1)) <= 2): ?>
                    <li class="<?= $i == ($page ?? 1) ? 'active' : '' ?>">
                        <a href="<?= $txBase ?>page=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php elseif (abs($i - ($page ?? 1)) == 3): ?>
                    <li><span>...</span></li>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if (($page ?? 1) < $totalPages): ?>
                <li><a href="<?= $txBase ?>page=<?= ($page ?? 1) + 1 ?>"><i class="fas fa-chevron-right"></i></a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>
