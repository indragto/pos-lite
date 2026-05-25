<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p><?= e($transaction['invoice_no']) ?> — <?= formatDate($transaction['created_at'], 'd/m/Y H:i') ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('transactions') ?>" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i>Back
        </a>
        <a href="<?= url("transactions/receipt/{$transaction['id']}") ?>" target="_blank" class="btn btn-outline">
            <i class="fas fa-print"></i>Print Receipt
        </a>
        <?php if (hasPermission('transactions.delete') && ($transaction['status'] ?? '') !== 'voided'): ?>
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#voidModal">
            <i class="fas fa-ban"></i>Void
        </button>
        <?php endif; ?>
    </div>
</div>

<!-- Transaction Info & Payment -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-info-circle text-primary me-2"></i>Transaction Info</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-muted" style="font-size:12px;">Invoice</div>
                        <div class="fw-bold"><?= e($transaction['invoice_no']) ?></div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted" style="font-size:12px;">Date & Time</div>
                        <div class="fw-bold"><?= formatDate($transaction['created_at']) ?></div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted" style="font-size:12px;">Cashier</div>
                        <div class="fw-bold"><?= e($transaction['cashier_name'] ?? '—') ?></div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted" style="font-size:12px;">Status</div>
                        <div>
                            <span class="badge <?= ($transaction['status'] ?? '') === 'voided' ? 'badge-danger' : 'badge-success' ?>">
                                <?= ucfirst($transaction['status'] ?? 'completed') ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-wallet text-success me-2"></i>Payment Details</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-muted" style="font-size:12px;">Method</div>
                        <?php
                        $pBadge = match ($transaction['payment_method'] ?? '') {
                            'cash' => 'badge-success',
                            'card' => 'badge-primary',
                            'qris' => 'badge-info',
                            default => 'badge-warning',
                        };
                        $pIcon = match ($transaction['payment_method'] ?? '') {
                            'cash' => 'fa-money-bill',
                            'card' => 'fa-credit-card',
                            'qris' => 'fa-qrcode',
                            default => 'fa-wallet',
                        };
                        ?>
                        <span class="badge <?= $pBadge ?>">
                            <i class="fas <?= $pIcon ?> me-1"></i><?= ucfirst($transaction['payment_method']) ?>
                        </span>
                    </div>
                    <div class="col-6">
                        <div class="text-muted" style="font-size:12px;">Items</div>
                        <div class="fw-bold"><?= count($transaction['items'] ?? []) ?> product(s)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Items Table -->
<div class="card mb-4">
    <div class="card-header">
        <h6><i class="fas fa-list me-2"></i>Items</h6>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($transaction['items'])): ?>
                    <?php foreach ($transaction['items'] as $item): ?>
                    <tr>
                        <td class="fw-bold"><?= e($item['product_name']) ?></td>
                        <td><span class="badge bg-light text-dark"><?= e($item['sku'] ?? '—') ?></span></td>
                        <td class="text-center"><?= number_format($item['quantity']) ?></td>
                        <td class="text-end"><?= formatRupiah($item['price']) ?></td>
                        <td class="text-end fw-bold"><?= formatRupiah($item['subtotal']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="fas fa-box-open"></i>
                                <h5>No items</h5>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Payment Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-6 offset-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-bold"><?= formatRupiah($transaction['subtotal'] ?? 0) ?></span>
                </div>
                <?php if (!empty($transaction['tax'])): ?>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tax</span>
                    <span class="fw-bold"><?= formatRupiah($transaction['tax']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($transaction['discount'])): ?>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Discount</span>
                    <span class="fw-bold text-danger">-<?= formatRupiah($transaction['discount']) ?></span>
                </div>
                <?php endif; ?>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold fs-5">Total</span>
                    <span class="fw-bold fs-5 text-primary"><?= formatRupiah($transaction['total']) ?></span>
                </div>
                <?php if (($transaction['payment_method'] ?? '') === 'cash'): ?>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Amount Paid</span>
                    <span class="fw-bold"><?= formatRupiah($transaction['amount_paid'] ?? 0) ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Change</span>
                    <span class="fw-bold text-success"><?= formatRupiah(($transaction['amount_paid'] ?? 0) - ($transaction['total'] ?? 0)) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Void Info -->
<?php if (($transaction['status'] ?? '') === 'voided'): ?>
<div class="card mb-4">
    <div class="card-body">
        <h6 class="text-danger"><i class="fas fa-ban me-2"></i>Void Information</h6>
        <p class="mb-1"><strong>Reason:</strong> <?= e($transaction['void_reason'] ?? '—') ?></p>
        <?php if (!empty($transaction['voided_by'])): ?>
        <p class="mb-0"><strong>Voided by:</strong> <?= e($transaction['voided_by']) ?> on <?= formatDate($transaction['voided_at']) ?></p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Void Modal -->
<?php if (hasPermission('transactions.delete') && ($transaction['status'] ?? '') !== 'voided'): ?>
<div class="modal fade" id="voidModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-ban text-danger me-2"></i>Void Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url("transactions/void/{$transaction['id']}") ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Void invoice <strong><?= e($transaction['invoice_no']) ?></strong>?</p>
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
