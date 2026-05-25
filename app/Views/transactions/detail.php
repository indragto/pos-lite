 <?php
/**
 * Transaction Detail View
 * Variables: $title, $transaction, $items
 */
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= url('transactions') ?>" class="btn btn-outline-secondary me-2" style="min-height: 44px;">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
            <span class="badge bg-primary fs-6 px-3 py-2"><?= e($transaction['invoice_no']) ?></span>
        </div>
        <div class="btn-group">
            <a href="<?= url('transactions/receipt/' . $transaction['id']) ?>" target="_blank"
               class="btn btn-outline-info" style="min-height: 44px;">
                <i class="fas fa-print me-1"></i>Print Receipt
            </a>
            <?php if (hasPermission('transactions.delete') && $transaction['status'] !== 'voided'): ?>
            <button class="btn btn-outline-danger" style="min-height: 44px;"
                    data-bs-toggle="modal" data-bs-target="#voidModal">
                <i class="fas fa-ban me-1"></i>Void
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Transaction Info -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i>Transaction Info</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Invoice No</small>
                            <strong><?= e($transaction['invoice_no']) ?></strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Date & Time</small>
                            <strong><?= formatDate($transaction['created_at']) ?></strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Cashier</small>
                            <strong><?= e($transaction['cashier_name'] ?? '-') ?></strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Status</small><br>
                            <span class="badge <?= $transaction['status'] === 'voided' ? 'bg-danger' : 'bg-success' ?> fs-6 px-3 py-2">
                                <?= ucfirst($transaction['status']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-wallet me-2 text-success"></i>Payment Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Payment Method</small><br>
                            <span class="badge
                                <?= $transaction['payment_method'] === 'cash' ? 'bg-success' : '' ?>
                                <?= $transaction['payment_method'] === 'card' ? 'bg-primary' : '' ?>
                                <?= $transaction['payment_method'] === 'qris' ? 'bg-info' : '' ?>
                                fs-6 px-3 py-2">
                                <i class="fas fa-<?= $transaction['payment_method'] === 'cash' ? 'money-bill' : '' ?><?= $transaction['payment_method'] === 'card' ? 'credit-card' : '' ?><?= $transaction['payment_method'] === 'qris' ? 'qrcode' : '' ?> me-1"></i>
                                <?= ucfirst($transaction['payment_method']) ?>
                            </span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Total Items</small>
                            <strong><?= number_format($transaction['total_items'] ?? 0) ?></strong>
                        </div>
                        <div class="col-12">
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Subtotal</span>
                                <strong><?= formatRupiah($transaction['subtotal'] ?? 0) ?></strong>
                            </div>
                            <?php if (!empty($transaction['discount'])): ?>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Discount</span>
                                <strong class="text-danger">-<?= formatRupiah($transaction['discount']) ?></strong>
                            </div>
                            <?php endif; ?>
                            <hr>
                            <div class="d-flex justify-content-between fs-5">
                                <span class="fw-bold">Total</span>
                                <span class="fw-bold text-primary"><?= formatRupiah($transaction['total']) ?></span>
                            </div>
                            <?php if ($transaction['payment_method'] === 'cash'): ?>
                            <div class="d-flex justify-content-between mt-2">
                                <span class="text-muted">Cash Received</span>
                                <strong><?= formatRupiah($transaction['amount_paid'] ?? 0) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Change</span>
                                <strong class="text-success"><?= formatRupiah(($transaction['amount_paid'] ?? 0) - $transaction['total']) ?></strong>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 py-3">
            <h6 class="mb-0 fw-bold"><i class="fas fa-list me-2"></i>Items</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Product</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Price</th>
                            <th class="text-end pe-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="ps-3">
                                <div>
                                    <span class="fw-semibold"><?= e($item['product_name']) ?></span>
                                    <br><small class="text-muted"><?= e($item['sku'] ?? '') ?></small>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark fs-6 px-3 py-2"><?= number_format($item['quantity']) ?></span>
                            </td>
                            <td class="text-end"><?= formatRupiah($item['price']) ?></td>
                            <td class="text-end pe-3 fw-bold"><?= formatRupiah($item['subtotal']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="3" class="ps-3 fw-bold">Total</td>
                            <td class="text-end pe-3 fw-bold text-primary"><?= formatRupiah($transaction['total']) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <?php if ($transaction['status'] === 'voided'): ?>
    <div class="card border-danger shadow-sm mt-4">
        <div class="card-body">
            <h6 class="text-danger"><i class="fas fa-ban me-2"></i>Void Information</h6>
            <p class="mb-1"><strong>Reason:</strong> <?= e($transaction['void_reason'] ?? '-') ?></p>
            <?php if (!empty($transaction['voided_by'])): ?>
            <p class="mb-0"><strong>Voided by:</strong> <?= e($transaction['voided_by']) ?> on <?= formatDate($transaction['voided_at']) ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if (hasPermission('transactions.delete') && $transaction['status'] !== 'voided'): ?>
<!-- Void Modal -->
<div class="modal fade" id="voidModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-ban me-2"></i>Void Transaction</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('transactions/void/' . $transaction['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>You are about to void invoice <strong><?= e($transaction['invoice_no']) ?></strong>.</p>
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