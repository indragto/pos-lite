<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1">Transactions</h5>
            <p class="text-muted mb-0">Transaction history and management</p>
        </div>
        <?php if (hasPermission('transactions.pos')): ?>
        <a href="<?= url('pos') ?>" class="btn btn-success" style="min-height: 44px;">
            <i class="fas fa-shopping-cart me-2"></i>New Sale
        </a>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= url('transactions') ?>" class="row g-3">
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted">From Date</label>
                    <input type="date" class="form-control" name="start_date" value="<?= e($startDate) ?>">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted">To Date</label>
                    <input type="date" class="form-control" name="end_date" value="<?= e($endDate) ?>">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted">Cashier</label>
                    <select class="form-select" name="user_id">
                        <option value="">All Cashiers</option>
                        <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id'] ?>" <?= ($userId ?? '') == $user['id'] ? 'selected' : '' ?>>
                            <?= e($user['full_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1" style="min-height: 44px;">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="<?= url('transactions') ?>" class="btn btn-outline-secondary" style="min-height: 44px;">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Invoice</th>
                            <th>Date & Time</th>
                            <th class="d-none d-md-table-cell">Cashier</th>
                            <th class="text-end">Total</th>
                            <th class="d-none d-sm-table-cell">Payment</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($transactions)): ?>
                            <?php foreach ($transactions as $txn): ?>
                            <tr>
                                <td class="ps-3">
                                    <a href="<?= url("transactions/{$txn['id']}") ?>" class="text-decoration-none fw-semibold">
                                        <?= e($txn['invoice_no']) ?>
                                    </a>
                                </td>
                                <td>
                                    <?= formatDate($txn['created_at'], 'd/m/Y') ?>
                                    <br><small class="text-muted"><?= formatDate($txn['created_at'], 'H:i') ?></small>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2"
                                             style="width: 32px; height: 32px;">
                                            <span class="text-primary fw-bold small">
                                                <?= strtoupper(substr($txn['cashier_name'] ?? 'U', 0, 1)) ?>
                                            </span>
                                        </div>
                                        <span><?= e($txn['cashier_name'] ?? 'Unknown') ?></span>
                                    </div>
                                </td>
                                <td class="text-end fw-bold"><?= formatRupiah($txn['total']) ?></td>
                                <td class="d-none d-sm-table-cell">
                                    <span class="badge
                                        <?= $txn['payment_method'] === 'cash' ? 'bg-success' : '' ?>
                                        <?= $txn['payment_method'] === 'card' ? 'bg-primary' : '' ?>
                                        <?= $txn['payment_method'] === 'qris' ? 'bg-info' : '' ?>
                                    ">
                                        <i class="fas fa-<?= $txn['payment_method'] === 'cash' ? 'money-bill' : '' ?><?= $txn['payment_method'] === 'card' ? 'credit-card' : '' ?><?= $txn['payment_method'] === 'qris' ? 'qrcode' : '' ?> me-1"></i>
                                        <?= ucfirst($txn['payment_method']) ?>
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group" role="group">
                                        <a href="<?= url("transactions/{$txn['id']}") ?>"
                                           class="btn btn-sm btn-outline-primary"
                                           style="min-height: 44px; min-width: 44px;" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= url("transactions/receipt/{$txn['id']}") ?>" target="_blank"
                                           class="btn btn-sm btn-outline-info"
                                           style="min-height: 44px; min-width: 44px;" title="Print Receipt">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <?php if (hasPermission('transactions.delete') && $txn['status'] !== 'voided'): ?>
                                        <button class="btn btn-sm btn-outline-danger"
                                                style="min-height: 44px; min-width: 44px;" title="Void"
                                                data-bs-toggle="modal" data-bs-target="#voidModal<?= $txn['id'] ?>">
                                            <i class="fas fa-ban"></i>
                                        </button>

                                        <!-- Void Modal -->
                                        <div class="modal fade" id="voidModal<?= $txn['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title"><i class="fas fa-ban me-2"></i>Void Transaction</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="<?= url("transactions/void/{$txn['id']}") ?>" method="POST">
                                                        <?= csrf_field() ?>
                                                        <div class="modal-body">
                                                            <p>You are about to void invoice <strong><?= e($txn['invoice_no']) ?></strong>.</p>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Void Reason <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" name="void_reason" rows="3" required
                                                                          placeholder="Enter reason for voiding this transaction"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="min-height: 44px;">Cancel</button>
                                                            <button type="submit" class="btn btn-danger" style="min-height: 44px;">
                                                                <i class="fas fa-ban me-1"></i>Void Transaction
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
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-receipt fa-3x mb-3 d-block opacity-50"></i>
                                    <p class="mb-1">No transactions found</p>
                                    <small>Try adjusting your date range or filters</small>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
